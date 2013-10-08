<?php

use Mockery as m;
use DayleRees\ContainerDebug\Command;

class CommandTest extends PHPUnit_Framework_TestCase
{
    public function testCommandCanBeCreated()
    {
        $command = new Command;
    }

    public function testContainerBindingsCanBeRetrieved()
    {
        $container = m::mock('Illuminate\Container\Container');
        $container->shouldReceive('getBindings')
                  ->once()
                  ->andReturn(array(
                    'chocolate' => 'sweet',
                    'apple'     => 'fruit'
                  ));
        $command = new Command;
        $command->setLaravel($container);
        $result = $command->getContainerBindings();
        $this->assertEquals(array(
            'apple'     => 'fruit',
            'chocolate' => 'sweet'
        ), $result);
    }

    public function testThatAServiceCanBeResolved()
    {
        $container = m::mock('Illuminate\Container\Container');
        $container->shouldReceive('make')
                  ->once()
                  ->andReturn('bar');
        $command = new Command;
        $command->setLaravel($container);
        $result = $command->resolveService('foo');
        $this->assertEquals('bar', $result);
    }

    public function testThatAServiceResolutionTimeCanBeCalculated()
    {
        $container = m::mock('Illuminate\Container\Container');
        $container->shouldReceive('make')
                  ->once()
                  ->andReturn('bar');
        $command = new Command;
        $command->setLaravel($container);
        $result = $command->calculateServiceResolutionTime('foo');
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
