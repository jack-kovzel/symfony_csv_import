<?php

namespace AppBundle\Command;

use Ddeboer\DataImport\Exception\ValidationException;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Step\FilterStep;
use Ddeboer\DataImport\Step\MappingStep;
use Ddeboer\DataImport\Step\ValidatorStep;
use Ddeboer\DataImport\Step\ValueConverterStep;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\Workflow\StepAggregator as Workflow;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Validator\Constraints as Assert;

use AppBundle\Exception\CostAndStockException;
use Symfony\Component\Validator\ConstraintViolation;

class ImportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:import')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $reader = new CsvReader(new \SplFileObject('/var/www/tt/app/stock_valid.csv'));
        $reader->setHeaderRowNumber(0);

        $writer = new DoctrineWriter($em, 'AppBundle:Product');
        if(false) {
            $writer = new ArrayWriter($testArray = []);
        }

		$validator = $this->getContainer()->get('validator');

		$filter = new ValidatorStep($validator);
		$filter->throwExceptions();
		$filter
			->add('Product Code', new Assert\NotBlank())
			->add('Product Name', new Assert\NotBlank())
			->add('Product Description', new Assert\Type('string'))
			->add('Stock', new Assert\Type('string'))

			->add('Cost in GBP', new Assert\Type('string'))
			->add('Cost in GBP', new Assert\LessThan([
				'value' => 1000,
				'message' => 'Cost should be less than {{ compared_value }}',
			]))

			->add('Discontinued', new Assert\Type('string'));

        $converterStep = new ValueConverterStep();
        $converterStep
            ->add('[Discontinued]', function ($v) {
                return $v == 'yes' ? new \DateTime() : null;
            });

		$costAndStockFilter = new FilterStep();
		$costAndStockFilter->add(function ($item) {
			if($item['Cost in GBP'] < 5 && $item['Stock'] < 10) {
				throw new CostAndStockException();
			}
		});

		$mappingStep = new MappingStep([
			'[Product Code]' => '[strProductCode]',
			'[Product Name]' => '[strProductName]',
			'[Product Description]' => '[strProductDesc]',
			'[Stock]' => '[intStock]',
			'[Cost in GBP]' => '[fltCost]',
			'[Discontinued]' => '[dtmDiscontinued]',
		]);

		$workflow = new Workflow($reader);
		$workflow->setSkipItemOnFailure(true);

        $result = $workflow
			->addStep($filter)
			->addStep($mappingStep)
			->addStep($costAndStockFilter)
			->addStep($converterStep)

			->addWriter($writer)
			->process();

		$message = sprintf('Total: %s objects. Imported: %s, not imported: %s',
			$result->getTotalProcessedCount(),
			$result->getSuccessCount(),
			$result->getErrorCount()
		);

		$output->writeln($message);

		if($result->hasErrors()) {
			$output->writeln('Products that not be imported: ');
			foreach($result->getExceptions() as $exception) {
				if($exception instanceof ValidationException) {
					/* @var $violation ConstraintViolation */
					foreach($exception->getViolations() as $violation) {
						$message = sprintf('Product: %s, message: %s',
							'['.implode(', ', $violation->getRoot()).']',
							$violation->getMessage()
						);

						$output->writeln($message);
					}
				}
			}
		}
    }
}
