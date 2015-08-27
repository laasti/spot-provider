<?php

namespace Laasti\SpotProvider\Tests;

use Laasti\SpotProvider\SpotProvider;
use League\Container\Container;

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
        $container->add('config.connections', [
            [
                'name' => 'default',
                'dsn' => 'mysql://root:@localhost/nodb'
            ],
            [
                'name' => 'mysql2',
                'dsn' => 'mysql://root:@localhost/nodb2'
            ],
        ]);
        $container->addServiceProvider(new SpotProvider);
        $config = $container->get('Spot\Config');
        $locator = $container->get('Spot\Locator');
        $config2 = $container->get('spot.config.default');
        $locator2 = $container->get('spot.locator.default');

        $this->assertTrue($locator instanceof \Spot\Locator);
        $this->assertTrue($locator2 instanceof \Spot\Locator);
        $this->assertTrue($config instanceof \Spot\Config);
        $this->assertTrue($config2 instanceof \Spot\Config);
    }

}
