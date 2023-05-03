# Ping Tree Lead Distribution

[![Latest Version on Packagist](https://img.shields.io/packagist/v/graymatterlabs/pingtree.svg?style=flat-square)](https://packagist.org/packages/graymatterlabs/pingtree)
[![Tests](https://github.com/graymatterlabs/pingtree/actions/workflows/run-tests.yml/badge.svg?branch=master)](https://github.com/graymatterlabs/pingtree/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/graymatterlabs/pingtree.svg?style=flat-square)](https://packagist.org/packages/graymatterlabs/pingtree)

A Ping Tree is a process in which a single lead is offered to multiple buyers in real-time and sold to the highest bidder.

## Installation

You can install the package via composer:

```bash
composer require graymatterlabs/pingtree:^0.3.0
```

## Usage

```php
$tree = new Tree($strategy, $offers);

$response = $tree->ping($lead);
```

### Responses
The ping tree will return the first successful response provided by an offer. Responses must implement the `GrayMatterLabs\PingTree\Contracts\Response` interface but should be customized beyond that.

```php
$tree = new Tree($strategy, $offers);

$response = $tree->ping($lead);

if ($response instanceof RedirectResponse) {
  return redirect($response->url());
}
```

### Offers
Offer instances are responsible for communicating with and managing state of the offer such as health and eligibility rules.

```php
<?php

namespace App\Offers;

use App\Http;
use App\Leads\AutoLead;
use GrayMatterLabs\PingTree\Contracts\Offer;
use GrayMatterLabs\PingTree\Contracts\Lead;
use GrayMatterLabs\PingTree\Contracts\Response;

class Example implements Offer
{
    private string $url = 'https://example.com';
    
    public function __construct(private Http $http, private string $key)
    {
    }

    public function getIdentifier(): string|int
    {
        return 'example';
    }

    public function ping(AutoLead $lead): int
    {
        $response = $this->http
            ->withHeader('Authorization', $this->key)
            ->post($this->url . '/value', [
                // ...
            ]);
        
        return (int) $response->json('value');
    }

    public function send(AutoLead $lead): Response
    {
        $response = $this->http
            ->withHeader('Authorization', $this->key)
            ->post($this->url . '/send', [
                // ...
            ]);

        return new RedirectResponse(!$response->ok(), $response->json('accepted'), $response->json('url'));
    }

    public function isEligible(AutoLead $lead): bool
    {
        return $lead->validate();
    }
    
    public function isHealthy(): bool
    {
        return $this->http->get($this->url . '/health')->ok();
    }
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
|--------------|-------------------------------------------|------------------------------------------------------------|
| `pinging`    | An offer is being selected                | Strategy $strategy, Lead $lead, array $offers              |
| `sending`    | The lead is being sent to the offer       | Lead $lead, Offer $offer                                   |
| `attempting` | A request to the offer is being attempted | Lead $lead, Offer $offer, int $attempt                     |
| `failed`     | A request to the offer failed             | Lead $lead, Offer $offer, Response $response, int $attempt |
| `rejected`   | The offer rejected the lead               | Lead $lead, Offer $offer, Response $response, int $attempt |
| `accepted`   | * The offer accepted the lead             | Lead $lead, Offer $offer, Response $response, int $attempt |

\* = will fire a maximum of *once* per execution

### Strategies
This package provides a concept of "strategies" to decide which offer to send the lead to. A default set of strategies are provided out-of-the-box. The only requirement to providing your own strategies is that they implement the `GrayMatterLabs\PingTree\Contracts\Strategy` interface.

| Strategy    | Description                                                                               |
|-------------|-------------------------------------------------------------------------------------------|
| HighestPing | Gets the offer with the highest `ping()` value                                            |
| RoundRobin  | A decorator to ensure each offer is attempted across multiple executions for a given lead |
| Shuffled    | A decorator to ensure random order before executing the given strategy                    |
| Ordered     | Gets the first offer in the list of provided offers                                       |

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
