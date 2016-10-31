<?php

namespace AppBundle\Command;

use AppBundle\Exception\FormatNotFoundException;
use AppBundle\Factory\ReaderFactory;
use Ddeboer\DataImport\Writer;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

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
        $file = $input->getArgument(self::ARGUMENT_FILE);

        try {
            $reader = ReaderFactory::getReader($format, $file);
        } catch (FormatNotFoundException $ex) {
            // if format is invalid
            $output->writeln('<error>Reader for type '.$format.' not found</error>');

            return;
        } catch (FileNotFoundException $ex) {
            //if file not found
            $output->writeln('<error>File not found</error>');

            return;
        }

        $productImportService = $this->getContainer()->get('product.import');
        $productImportService->doImport($reader, $this->getWriter($input));

        $message = sprintf('Total: %s objects. Imported: %s, not imported: %s',
            $productImportService->getTotalProcessedCount(),
            $productImportService->getSuccessCount(),
            $productImportService->getUniqueErrorsCount()
        );
        $output->writeln($message);

        if ($input->getOption('verbose')) {
            $output->writeln('Products that not be imported: ');
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
        } else {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $writer = new DoctrineWriter($em, 'AppBundle:Product', 'productCode');
            $writer->setTruncate(false);

            return $writer;
        }
    }
}
