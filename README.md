# Houston
### We've got a problem

[![Latest Version](https://img.shields.io/github/release/edwin-luijten/houston.svg?style=flat)](https://github.com/Edwin-Luijten/houston/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/Edwin-Luijten/houston/master.svg?style=flat-square)](https://travis-ci.org/Edwin-Luijten/houston)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Edwin-Luijten/houston.svg?style=flat-square)](https://scrutinizer-ci.com/g/Edwin-Luijten/houston/?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/Edwin-Luijten/houston.svg?style=flat-square)](https://scrutinizer-ci.com/g/Edwin-Luijten/houston/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/edwin-luijten/houston.svg?style=flat-square)](https://packagist.org/packages/edwin-luijten/houston)

## Install

Via Composer

``` bash
$ composer require edwin-luijten/houston
```

## Usage

### Enable Houston

By default Houston uses the `RotatingFileHandler` class from monolog,  
and logs to /var/log/houston-{date}.problem.

```php
Houston::init();
```

To overwrite the default log path for the `RotatingFileHandler`:
```php
Houston::init([
    'file_log_location' => __DIR__ . '/my-log-folder/my-log.txt',
]);
```

### Handlers

You can use all the handlers available in the monolog package:
```php
Houston::init([
    'handlers' => [
        new RotatingFileHandler('logpath'),
        new RedisHandler($redisClient, $key),
    ]
]);
```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [Edwin Luijten](https://github.com/Edwin-Luijten)
- [All Contributors](https://github.com/Edwin-Luijten/houston/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.