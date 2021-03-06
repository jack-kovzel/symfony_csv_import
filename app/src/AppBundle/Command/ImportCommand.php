<?php

namespace AppBundle\Command;

use AppBundle\Entity\Product;
use AppBundle\Exception\FormatNotFoundException;
use AppBundle\Factory\ReaderFactory;
use AppBundle\Traits\RegistryTrait;
use Ddeboer\DataImport\Writer;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ImportCommand extends ContainerAwareCommand
{
    use RegistryTrait;

    const ARGUMENT_FILE = 'file';
    const ARGUMENT_FORMAT = 'format';

    const OPTION_TEST = 'test';

    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct();

        $this->setDoctrine($registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:import')
            ->setDescription('Import csv file into product table')
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
                'If you want to run in a test mode'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $format = $input->getArgument(self::ARGUMENT_FORMAT);
        $file = $input->getArgument(self::ARGUMENT_FILE);

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

        $productImportService = $this->getContainer()->get('product.import');
        $productImportService->doImport($reader, $this->getWriter($input));

        $message = sprintf(
            'Total: %s objects. Imported: %s, not imported: %s',
            $productImportService->getTotalProcessedCount(),
            $productImportService->getSuccessCount(),
            $productImportService->getUniqueErrorsCount()
        );
        $output->writeln($message);

        if ($input->getOption('verbose')) {
            $output->writeln('Not imported products: ');
            
            foreach ($productImportService->getErrors() as $error) {
                $output->writeln($error->getFullMessage());
            }
        }
    }

    /**
     * @param InputInterface $input
     *
     * @return Writer
     */
    protected function getWriter(InputInterface $input)
    {
        if ($input->getOption(self::OPTION_TEST)) {
            $arrayForWrite = [];
            $writer = new ArrayWriter($arrayForWrite);

            return $writer;
        }

        $writer = new DoctrineWriter(
            $this->getDoctrine()->getEntityManager(),
            Product::class,
            'productCode'
        );

        $writer->setTruncate(false);

        return $writer;
    }
}
