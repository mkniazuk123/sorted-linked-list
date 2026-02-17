<?php

namespace MKniazuk\SortedLinkedList;

/**
 * @template T of scalar
 *
 * @internal
 */
readonly class ScalarItem implements Item
{
    /**
     * @param T $value
     */
    public function __construct(
        public bool|float|int|string $value,
    ) {}

    public function compare(Item $other): int
    {
        if (!$other instanceof ScalarItem) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot compare %s with %s',
                self::class,
                get_class($other),
            ));
        }

        return $this->value <=> $other->value;
    }
}
