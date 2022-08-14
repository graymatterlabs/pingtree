# Ping Tree Lead Distribution

[![Latest Version on Packagist](https://img.shields.io/packagist/v/graymatterlabs/pingtree.svg?style=flat-square)](https://packagist.org/packages/graymatterlabs/pingtree)
[![Tests](https://github.com/graymatterlabs/pingtree/actions/workflows/run-tests.yml/badge.svg?branch=master)](https://github.com/graymatterlabs/pingtree/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/graymatterlabs/pingtree.svg?style=flat-square)](https://packagist.org/packages/graymatterlabs/pingtree)

A Ping Tree is a process in which a single lead is offered to multiple buyers in real-time and sold to the highest bidder.

## Installation

You can install the package via composer:

```bash
composer require graymatterlabs/pingtree:^0.1
```

## Usage

```php
$tree = new Tree($strategy, $offers);

$response = $tree->ping($lead);

if ($response instanceof RedirectResponse) {
    redirect($response->getRedirect());
}
```

For examples of usage and implementation, please check out the `tests/` directory.

## Testing

```bash
composer test
```

## Changelog

Please see the [Release Notes](../../releases) for more information on what has changed recently.

## Credits

- [Ryan Colson](https://github.com/ryancco)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
