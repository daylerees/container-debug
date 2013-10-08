<?php

namespace DayleRees\ContainerDebug;

use Exception;
use Illuminate\Console\Application;
use Illuminate\Container\Container;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Console\Command as IlluminateCommand;

class Command extends IlluminateCommand
{
    /**
     * Heading to use for the identifier table column.
     */
    const IDENTIFIER_HEADING = 'Identifier';

    /**
     * Heading to use for the service table column.
     */
    const SERVICE_HEADING = 'Service';

    /**
     * Heading to use for the resolution time table column.
     */
    const TIME_HEADING = 'Resolution time (ms)';

    /**
     * Format for displaying a scalar value.
     */
    const SCALAR_FORMAT = '<%s> "%s"';

    /**
     * Format for displaying a non scalar value.
     */
    const NON_SCALAR_FORMAT = '<%s>';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'container:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View the contents of the IoC container.';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function fire()
    {
        $services = $this->getContainerBindings();
        $table = $this->buildServiceTable($services);
        $table->render($this->output);
    }

    /**
     * Construct an ASCII table to display services.
     *
     * @param  array $services
     * @return TableHelper
     */
    public function buildServiceTable($services)
    {
        $table = new TableHelper;
        $table->setHeaders($this->buildTableHeaders());
        $table->setRows($this->buildTableRows($services));
        return $table;
    }

    /**
     * Build the column headers for the services table.
     *
     * @return array
     */
    public function buildTableHeaders()
    {
        return array(
            self::IDENTIFIER_HEADING,
            self::SERVICE_HEADING,
            self::TIME_HEADING
        );
    }

    /**
     * Build the rows for the services table.
     *
     * @param  array $services
     * @return array
     */
    public function buildTableRows($services)
    {
        $rows = array();
        foreach (array_keys($services) as $identifier) {
            try {
                $service = $this->resolveService($identifier);
                $rows[] = array(
                    $identifier,
                    $this->getServiceDescription($service),
                    $this->calculateServiceResolutionTime($identifier)
                );
            }
            catch (Exception $e) {
                $rows[] = array($identifier, 'Unable to resolve service.', 'N/A');
            }
        }
        return $rows;
    }

    /**
     * Retreive an array of container bindings in alphabetical order.
     *
     * @return array
     */
    public function getContainerBindings()
    {
        $bindings = $this->laravel->getBindings();
        ksort($bindings);
        return $bindings;
    }

    /**
     * Resolve a service from the container by its identifier.
     *
     * @param  string $identifier
     * @return mixed
     */
    public function resolveService($identifier)
    {
        return $this->laravel->make($identifier);
    }

    /**
     * Calculate the time to resolve a service in microseconds.
     *
     * @param  string $identifier
     * @return string
     */
    public function calculateServiceResolutionTime($identifier)
    {
        $before = microtime(true);
        $this->resolveService($identifier);
        return number_format((microtime(true) - $before) * 1000, 3);
    }

    /**
     * Retrieve a string representation of a service.
     *
     * @param  mixed  $service
     * @return string
     */
    public function getServiceDescription($service)
    {
        if ($this->serviceIsObject($service)) {
            return get_class($service);
        }
        return $this->formatServiceDescription($service, is_scalar($service));
    }

    /**
     * Retrieve a suitable formatted service description.
     *
     * @param  mixed  $service
     * @param  bool   $scalar
     * @return string
     */
    public function formatServiceDescription($service, $scalar = true)
    {
        return sprintf(
            $scalar ? self::SCALAR_FORMAT : self::NON_SCALAR_FORMAT,
            gettype($service),
            $scalar ? (string) $service : null
        );
    }

    /**
     * Determine whether a service is an object.
     *
     * @param  mixed $service
     * @return bool
     */
    public function serviceIsObject($service)
    {
        return gettype($service) === 'object';
    }
}
