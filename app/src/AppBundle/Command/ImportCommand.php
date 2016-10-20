<?php

namespace AppBundle\Command;

use AppBundle\Entity\Product;
use AppBundle\Exception\FormatNotFoundException;
use AppBundle\Factory\ReaderFactory;
use Ddeboer\DataImport\Exception\ValidationException;
use Ddeboer\DataImport\Step\FilterStep;
use Ddeboer\DataImport\Step\MappingStep;
use AppBundle\Step\ValidatorStep;
use Ddeboer\DataImport\Step\ValueConverterStep;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\Workflow\StepAggregator as Workflow;
use Ddeboer\DataImport\Result;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Validator\Constraints as Assert;

use AppBundle\Exception\CostAndStockException;
use Symfony\Component\Validator\ConstraintViolation;

class ImportCommand extends ContainerAwareCommand
{
	const ARGUMENT_FILE = 'file';
	const ARGUMENT_FORMAT = 'format';

	const OPTION_TEST = 'test';

	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('app:import')
			->setDescription('Import cvs file to product table')
			->addArgument(
				self::ARGUMENT_FORMAT,
				InputArgument::REQUIRED,
				'Type a format: '
			)
			->addArgument(
				self::ARGUMENT_FILE,
				InputArgument::REQUIRED,
				'Type a path to file: '
			)
			->addOption(
				self::OPTION_TEST,
				null,
				InputOption::VALUE_NONE,
				'If you want run a test mode'
			);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$format = $input->getArgument(self::ARGUMENT_FORMAT);
		$file 	= $input->getArgument(self::ARGUMENT_FILE);

		try {
			$reader = ReaderFactory::getReader($format, $file);
		} catch (FormatNotFoundException $ex) {
			// if format is invalid
			$output->writeln('<error>Reader for type ' . $format . ' not found</error>');
			return;
		} catch (FileNotFoundException $ex) {
			//if file not found
			$output->writeln('<error>File not found</error>');
			return;
		}

		$reader->setHeaderRowNumber(0);

		$mappingStep = new MappingStep([
			'[Product Code]' => '[strProductCode]',
			'[Product Name]' => '[strProductName]',
			'[Product Description]' => '[strProductDesc]',
			'[Stock]' => '[intStock]',
			'[Cost in GBP]' => '[fltCost]',
			'[Discontinued]' => '[dtmDiscontinued]',
		]);

		$converterStep = new ValueConverterStep();
		$converterStep
			->add('[dtmDiscontinued]', function ($v) {
				return $v == 'yes' ? new \DateTime() : null;
			});

		$validator = $this->getContainer()->get('validator');
		$filter = new ValidatorStep($validator);
		$filter->throwExceptions();
		$filter->add('fltCost', new Assert\LessThan([
			'value' => 1000,
			'message' => 'Cost should be less than {{ compared_value }}',
		]));

		/* @var $metadata \Symfony\Component\Validator\Mapping\ClassMetadata */
		$metadata = $validator->getMetadataFor(new Product());
		foreach ($metadata->properties as $attribute => $propertyMetadata) {
			foreach ($propertyMetadata->getConstraints() as $constraint) {
				$filter->add($attribute, $constraint);
			}
		}


		$costAndStockFilter = new FilterStep();
		$costAndStockFilter->add(function ($item) {
			if($item['fltCost'] < 5 && $item['intStock'] < 10) {
				$message = sprintf('Product: %s, message: %s',
					$item['strProductCode'],
					'Cost < 5 and Stock < 10'
				);
				throw new CostAndStockException($message);
			}
		});

		$workflow = new Workflow($reader);
		$workflow->setSkipItemOnFailure(true);

        $result = $workflow
			->addStep($mappingStep, 4)
			->addStep($converterStep, 3)
			->addStep($filter, 2)
			->addStep($costAndStockFilter, 1)
			->addWriter($this->getWriter($input))
			->process();

		$message = sprintf('Total: %s objects. Imported: %s, not imported: %s',
			$result->getTotalProcessedCount(),
			$result->getSuccessCount(),
			$result->getErrorCount()
		);
		$output->writeln($message);

		if($input->getOption('verbose')) {
			$this->printErrors($output, $result);
		}
    }

	/**
	 * @param OutputInterface $output
	 * @param Result $result
	 */
	protected function printErrors(OutputInterface $output, $result)
	{
		if ($result->hasErrors()) {
			$output->writeln('Products that not be imported: ');
			foreach ($result->getExceptions() as $exception) {
				if ($exception instanceof ValidationException) {
					/* @var $violation ConstraintViolation */
					foreach ($exception->getViolations() as $violation) {
						$message = sprintf('Product code: %s, message: %s',
							$violation->getRoot()['strProductCode'],
							$violation->getMessage()
						);

						$output->writeln($message);
					}
				} elseif ($exception instanceof CostAndStockException) {
					$output->writeln($exception->getMessage());
				}
			}
		}
	}

	/**
	 * @param InputInterface $input
	 * @return ArrayWriter|DoctrineWriter
	 */
	protected function getWriter(InputInterface $input)
	{
		if ($input->getOption(self::OPTION_TEST)) {
			$arrayForWrite = [];
			$writer = new ArrayWriter($arrayForWrite);
			return $writer;
		} else {
			$em = $this->getContainer()->get('doctrine')->getManager();
			$writer = new DoctrineWriter($em, 'AppBundle:Product', 'strProductCode');
			$writer->setTruncate(false);
			return $writer;
		}
	}
}
