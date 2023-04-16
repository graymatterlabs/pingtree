<?php

declare(strict_types=1);

namespace GrayMatterLabs\PingTree\Tests\Mocks;

use GrayMatterLabs\PingTree\Tree;

class MockTree extends Tree
{
    protected int $maxTries = 1;

    protected int $backoff = 0;
}
