# Laasti/spot-provider

A league/container service provider for Spot ORM.

## Installation

```
composer require laasti/spot-provider
```

## Usage

```php

$config = [
    'name' => 'default', //required
    //String DSN
    'dsn' => '',
    //OR, full array
    'adapter'  => '',
    'host'     => '', //required
    'dbname'   => '', //required
    'user'     => '', //required
    'password' => '', //required
    'port'     => '',
    'protocol' => '',
    'socket'   => '',
    'dbsyntax' => '',
];

$container = new League\Container\Container();
$container->add('config.connections', [
    [
        'name' => 'default',
        //...
    ]
]);

$container->addServiceProvider('Laasti\SpotProvider\SpotProvider');

$container->get('Spot\Locator');//OR, $container->get('spot.locator.default');
$container->add('Acme\MyMapper')->withArguments(['Spot\Locator']);
$container->add('Acme\MyMsSqlMapper')->withArguments(['spot.locator.mssql']);

```

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D

## History

See CHANGELOG.md for more information.

## Credits

Author: Sonia Marquette (@nebulousGirl)

## License

Released under the MIT License. See LICENSE.md file.
