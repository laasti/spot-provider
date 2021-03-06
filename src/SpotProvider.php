<?php

namespace Laasti\SpotProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use RuntimeException;
use Spot\Config;
use Spot\Locator;

class SpotProvider extends AbstractServiceProvider
{

    protected $provides = [];
    
    protected $defaultProvides = [
        'Spot\Config',
        'Spot\Locator'
    ];

    public function register()
    {
        $di = $this->getContainer();

        $connections = $di->get('config')['connections'];

        $first = true;
        foreach ($connections as $connection) {
            $di->add('spot.config.' . $connection['name'], function($logger) use ($connection) {
                $cfg = new Config();
                $param = isset($connection['dsn']) ? $connection['dsn'] : $connection;
                $conn = $cfg->addConnection($connection['name'], $param);
                $sqlLogger = new \Laasti\SpotProvider\MonologSqlLogger($logger);
                $conn->getConfiguration()->setSQLLogger($sqlLogger);
                return $cfg;
            }, true)->withArgument('Psr\Log\LoggerInterface');

            $di->add('spot.locator.' . $connection['name'], function() use ($di, $connection) {
                $spot = new Locator($di->get('spot.config.' . $connection['name']));
                return $spot;
            }, true);

            if ($first) {
                $di->add('Spot\Config', function() use ($di, $connection) {
                    return $di->get('spot.config.' . $connection['name']);
                }, true);
                $di->add('Spot\Locator', function($config = null) use ($di, $connection) {
                    return $di->get('spot.locator.' . $connection['name'], [$config]);
                }, true);
                $first = false;
            }
        }
    }

    public function provides($alias = null)
    {
        if (!count($this->provides)) {
            $this->provides = $this->defaultProvides;
            try {
                $connections = $this->getContainer()->get('config')['connections'];

                if (!is_array($connections) || count($connections) === 0) {
                    throw new \Exception();
                }
            } catch (\Exception $e) {
                throw new RuntimeException('To use SpotProvider, you must add an array of connections to the container using the key "config.connections".');
            }

            foreach ($connections as $connection) {
                $this->provides[] = 'spot.config.' . $connection['name'];
                $this->provides[] = 'spot.locator.' . $connection['name'];
            }
        }

        return parent::provides($alias);
    }

}
