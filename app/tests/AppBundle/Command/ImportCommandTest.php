<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\ImportCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImportCommandTest extends KernelTestCase
{
    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $app = new Application($kernel);
        $app->add(new ImportCommand($this->createMock(RegistryInterface::class)));

        $command = $app->find('app:import');

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteWithBadFormat()
    {
        $this->commandTester->execute([
            'format' => 'cv',
            'file' => __DIR__.'/../Fixtures/stock_valid.csv',
            '--test' => true,
        ]);

        $this->assertEquals(
            'Reader for type cv not found' . PHP_EOL,
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithBadFile()
    {
        $this->commandTester->execute(
            array(
                'format' => 'csv',
                'file' => __DIR__.'/../Fixtures/stock.csv',
                '--test' => true,
            )
        );
        $this->assertEquals(
            'File not found' . PHP_EOL,
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithValidFormatAndFile()
    {
        $this->commandTester->execute(
            array(
                'format' => 'csv',
                'file' => __DIR__.'/../Fixtures/stock_valid.csv',
                '--test' => true,
            )
        );

        $this->assertEquals(
            'Total: 27 objects. Imported: 23, not imported: 4' . PHP_EOL,
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithWrongCostAndStock()
    {
        $this->commandTester->execute(
            array(
                'format' => 'csv',
                'file' => __DIR__.'/../Fixtures/items_with_invalid_cost_and_stock.csv',
                '--test' => true,
            )
        );

        $this->assertEquals(
            'Total: 3 objects. Imported: 2, not imported: 1' . PHP_EOL,
            $this->commandTester->getDisplay()
        );
    }
}
