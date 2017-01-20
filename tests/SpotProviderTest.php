<?php

namespace Laasti\SpotProvider;

use League\Container\Container;
use Spot\Config;
use Spot\Locator;

class SpotProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testNoConnections()
    {
        $container = new Container();
        $this->setExpectedException('RuntimeException');
        $container->addServiceProvider(new SpotProvider);

        $container->get('Spot\Locator');
    }

    public function testProvider()
    {
        $container = new Container();
        $container->add('config', [
            'connections' => [
                [
                    'name' => 'default',
                    'dsn' => 'mysql://root:@localhost/nodb'
                ],
                [
                    'name' => 'mysql2',
                    'dsn' => 'mysql://root:@localhost/nodb2'
                ],
            ]
        ]);
        $container->addServiceProvider(new SpotProvider);
        $config = $container->get('Spot\Config');
        $locator = $container->get('Spot\Locator');
        $config2 = $container->get('spot.config.default');
        $locator2 = $container->get('spot.locator.default');

        $this->assertTrue($locator instanceof Locator);
        $this->assertTrue($locator2 instanceof Locator);
        $this->assertTrue($config instanceof Config);
        $this->assertTrue($config2 instanceof Config);
    }
}
