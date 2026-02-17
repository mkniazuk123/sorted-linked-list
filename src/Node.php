<?php

namespace MKniazuk\SortedLinkedList;

/**
 * @template T of mixed
 *
 * @internal
 */
final class Node
{
    /**
     * @param T            $item
     * @param null|Node<T> $next
     */
    public function __construct(
        public readonly mixed $item,
        public ?Node $next = null,
    ) {}
}
