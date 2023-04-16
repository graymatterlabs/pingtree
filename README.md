# Ping Tree Lead Distribution

[![Latest Version on Packagist](https://img.shields.io/packagist/v/graymatterlabs/pingtree.svg?style=flat-square)](https://packagist.org/packages/graymatterlabs/pingtree)
[![Tests](https://github.com/graymatterlabs/pingtree/actions/workflows/run-tests.yml/badge.svg?branch=master)](https://github.com/graymatterlabs/pingtree/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/graymatterlabs/pingtree.svg?style=flat-square)](https://packagist.org/packages/graymatterlabs/pingtree)

A Ping Tree is a process in which a single lead is offered to multiple buyers in real-time and sold to the highest bidder.

## Installation

You can install the package via composer:

```bash
composer require graymatterlabs/pingtree:^0.2.0
```

## Usage

```php
$tree = new Tree($strategy, $offers);

$response = $tree->ping($lead);
```

### Offers
Offer instances are responsible for communicating with and managing state of the offer such as health and eligibility rules.

### Responses
The ping tree will return the first successful response provided by an offer. Responses must implement the `GrayMatterLabs\PingTree\Contracts\Response` interface but should be customized beyond that. For example, an offer that provides a URL to redirect a Lead to on success might return an instance of a custom `RedirectResponse` which might have a `url()` method to expose the URL. Here's how that might be used:
```php
$tree = new Tree($strategy, $offers);

$response = $tree->ping($lead);

if ($response instanceof RedirectResponse) {
  return redirect($response->url());
}
```

### Events
This package fires events and provides you the ability to register listeners for each. Listeners can be used for performing any custom logic. Listeners are executed synchronously to be sure to handle any potential exceptions.

Listeners can be registered to the Tree class, which handles all events, using the `listen` method, like so:
```php
$tree = new Tree($strategy, $offers);

// listen for any events
$tree->listen($event, $callable);
$tree->listen($event, $other_callable);

$response = $tree->ping($lead);
```

Below is a list of all events fired, their descriptions, and the parameters passed to any registered listeners.

| Name         | Description                               | Parameters                                                 |
| ------------ | ----------------------------------------- | ---------------------------------------------------------- |
| `pinging`    | An offer is being selected                | Strategy $strategy, Lead $lead, array $offers              |
| `sending`    | The lead is being sent to the offer       | Lead $lead, Offer $offer                                   |
| `attempting` | A request to the offer is being attempted | Lead $lead, Offer $offer, int $attempt                     |
| `failed`     | A request to the offer failed             | Lead $lead, Offer $offer, Response $response, int $attempt |
| `rejected`   | The offer rejected the lead               | Lead $lead, Offer $offer, Response $response, int $attempt |
| `accepted`   | * The offer accepted the lead             | Lead $lead, Offer $offer, Response $response, int $attempt |

\* = will fire a maximum of *once* per execution

### Strategies
This package provides a concept of "strategies" to decide which offer to send the lead to. A default set of strategies are provided out-of-the-box. The only require to provide your own strategies are that they implement the `GrayMatterLabs\PingTree\Contracts\Strategy` interface.

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
