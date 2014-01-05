<?php

use Mockery as m;
use Illuminate\Container\Container;
use DayleRees\ContainerDebug\Command;

class CommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->configure();
    }

    public function configure($prefix = '')
    {
        $this->container = new Container;
        $this->container['chocolate'] = 'sweet';
        $this->container['apple'] = 'fruit';

        $this->command = m::mock('DayleRees\ContainerDebug\Command[argument]');
        $this->command->setLaravel($this->container);
        $this->command->shouldReceive('argument')->with('prefix')->andReturn($prefix);
    }

    public function testCommandCanBeCreated()
    {
        $command = new Command;
    }

    public function testContainerBindingsCanBeRetrieved()
    {
        $result = $this->command->getContainerBindings();
        $this->assertEquals(array('apple', 'chocolate'), $result);
    }

    public function testRowsCanBeBuild()
    {
        $rows = $this->command->buildTableRows(array('apple', 'chocolate'));

        $this->assertCount(2, $rows);
        $this->assertEquals('apple', $rows[0][0]);
        $this->assertEquals('<string> "fruit"', $rows[0][1]);
        $this->assertEquals('chocolate', $rows[1][0]);
        $this->assertEquals('<string> "sweet"', $rows[1][1]);
    }

    public function testRowsCanBeFilteredWithPrefix()
    {
        $this->configure('choc');

        $rows = $this->command->buildTableRows(array('apple', 'chocolate'));

        $this->assertCount(1, $rows);
        $this->assertEquals('chocolate', $rows[0][0]);
        $this->assertEquals('<string> "sweet"', $rows[0][1]);
    }

    public function testExceptionDuringResolvingIsHandled()
    {
        $this->container['foo'] = function() { throw new \Exception; };

        $row = $this->command->buildServiceRow('foo');

        $this->assertEquals(
            array('foo', 'Unable to resolve service.', 'N/A'),
            $row
        );
    }

    public function testThatAServiceCanBeResolved()
    {
        $result = $this->command->resolveService('apple');
        $this->assertEquals('fruit', $result);
    }

    public function testThatAServiceResolutionTimeCanBeCalculated()
    {
        $result = $this->command->calculateServiceResolutionTime('apple');
        $this->assertTrue(is_numeric($result));
        $this->assertTrue($result > 0);
    }

    public function testAScalarServiceDescriptionCanBeRetrieved()
    {
        $command = new Command;
        $description = $command->getServiceDescription(3);
        $this->assertEquals('<integer> "3"', $description);
        $description = $command->getServiceDescription('foo');
        $this->assertEquals('<string> "foo"', $description);
    }

    public function testANonScalarServiceDescriptionCanBeRetrieved()
    {
        $command = new Command;
        $description = $command->getServiceDescription(array('foo'));
        $this->assertEquals('<array>', $description);
    }

    public function testAnObjectServiceDescriptionCanBeRetrieved()
    {
        $command = new Command;
        $obj = new stdClass;
        $description = $command->getServiceDescription($obj);
        $this->assertEquals('stdClass', $description);
    }

    public function testServiceDescriptionCanBeFormatted()
    {
        $command = new Command;
        $description = $command->formatServiceDescription('foo', false);
        $this->assertEquals('<string>', $description);
        $description = $command->formatServiceDescription('foo', true);
        $this->assertEquals('<string> "foo"', $description);
    }

    public function testThatAServiceCanBeIdentifiedAsAnObject()
    {
        $command = new Command;
        $result = $command->serviceIsObject(3);
        $this->assertFalse($result);
        $result = $command->serviceIsObject(new stdClass);
        $this->assertTrue($result);
    }
}
