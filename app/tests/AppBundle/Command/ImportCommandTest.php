<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\ImportCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ImportCommandTest
 * @package Tests\CsvBundle\Command
 */
class ImportCommandTest extends KernelTestCase
{
	/**
	 * @var CommandTester
	 */
	private $commandTester;

	public function setUp()
	{
		$kernel = $this->createKernel();
		$kernel->boot();

		$app = new Application($kernel);
		$app->add(new ImportCommand());

		$command = $app->find('app:import');

		$this->commandTester = new CommandTester($command);
	}

	/**
	 * Testing how command execute with invalid format
	 */
	public function testExecuteWithBadFormat()
	{
		$this->commandTester->execute(
			array(
				'format' => 'cv',
				'file' => __DIR__ . '/../Fixtures/stock_valid.csv',
				'--test' => true,
			)
		);
		$this->assertEquals('Reader for type cv not found' . PHP_EOL, $this->commandTester->getDisplay());
	}

	/**
	 * Testing how command execute with invalid file
	 */
	public function testExecuteWithBadFile()
	{
		$this->commandTester->execute(
			array(
				'format' => 'csv',
				'file' => __DIR__ . '/../Fixtures/stock.csv',
				'--test' => true,
			)
		);
		$this->assertEquals('File not found' . PHP_EOL, $this->commandTester->getDisplay());
	}

	/**
	 * Testing how command execute with valid format and file
	 */
	public function testExecute()
	{
		$this->commandTester->execute(
			array(
				'format' => 'csv',
				'file' => __DIR__ . '/../Fixtures/stock_valid.csv',
				'--test' => true,
			)
		);
		$this->assertEquals('Total: 27 objects. Imported: 25, not imported: 2' . PHP_EOL, $this->commandTester->getDisplay());
	}


	/**
	 * Test import with wrong item's which cost < 5$ and stock < 10
	 */
	public function testExecuteWithWrongCostAndStock()
	{
		$this->commandTester->execute(
			array(
				'format' => 'csv',
				'file' => __DIR__ . '/../Fixtures/items_with_invalid_cost_and_stock.csv',
				'--test' => true,
			)
		);

		$this->assertEquals('Total: 3 objects. Imported: 2, not imported: 1' . PHP_EOL, $this->commandTester->getDisplay());
	}
}