# Awesome Framework Core

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]
[![Maintainability](https://api.codeclimate.com/v1/badges/e21ef866fb5b23a90f58/maintainability)](https://codeclimate.com/github/jjuanrivvera/awesome-framework-core/maintainability)

## Structure

```
examples/
src/
tests/
vendor/
```


## Install

Via Composer

``` bash
$ composer require jjuanrivvera/awesome-framework-core
```

## Usage

``` php
// Bootstrap application
$app = new Awesome\App(
    config: new Awesome\Config(dirname(__FILE__) . '/config'), // Set config, optional
    routesPath: dirname(__FILE__) . '/routes/*.php', // Set routes path, optional
    viewPath: './App/Views', // Set views path, optional
    isCli: false // Define if the application is running as CLI, default false
);

// Initialize application
$app->init();

// Run application
return $app->run();
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email jjuanrivvera@gmail.com instead of using the issue tracker.

## Credits

- [jjuanrivvera99](https://github.com/jjuanrivvera99)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jjuanrivvera/awesome-framework-core.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
<!-- [ico-travis]: https://img.shields.io/travis/jjuanrivvera/awesome-framework-core/master.svg?style=flat-square -->
<!-- [ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/jjuanrivvera/awesome-framework-core.svg?style=flat-square -->
<!-- [ico-code-quality]: https://img.shields.io/scrutinizer/g/jjuanrivvera/awesome-framework-core.svg?style=flat-square -->
[ico-downloads]: https://img.shields.io/packagist/dt/jjuanrivvera/awesome-framework-core.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jjuanrivvera/awesome-framework-core
<!-- [link-travis]: https://travis-ci.org/jjuanrivvera/awesome-framework-core -->
<!-- [link-scrutinizer]: https://scrutinizer-ci.com/g/jjuanrivvera/awesome-framework-core/code-structure -->
<!-- [link-code-quality]: https://scrutinizer-ci.com/g/jjuanrivvera/awesome-framework-core -->
[link-downloads]: https://packagist.org/packages/jjuanrivvera/awesome-framework-core
[link-author]: https://github.com/jjuanrivvera99
