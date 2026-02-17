<?php

namespace MKniazuk\SortedLinkedList\Tests;

use MKniazuk\SortedLinkedList\Item;

/**
 * @internal
 */
readonly class TestItem implements Item
{
    public function __construct(public int $value) {}

    public function compare(Item $other): int
    {
        if (!$other instanceof self) {
            throw new \InvalidArgumentException(sprintf('Expected instance of %s, got %s', self::class, get_class($other)));
        }

        return $this->value <=> $other->value;
    }
}
