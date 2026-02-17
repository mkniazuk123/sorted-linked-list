<?php

namespace MKniazuk\SortedLinkedList;

/**
 * @template T of Item
 *
 * @extends \IteratorAggregate<int, T>
 */
interface SortedList extends \Countable, \IteratorAggregate
{
    public function isEmpty(): bool;

    /**
     * @return T[]
     */
    public function toArray(): array;

    /**
     * Get the first element of the list or throw an exception if the list is empty.
     *
     * @return T
     *
     * @throws NoSuchElementException
     */
    public function head(): Item;

    /**
     * Get the last element of the list or throw an exception if the list is empty.
     *
     * @return T
     *
     * @throws NoSuchElementException
     */
    public function tail(): Item;

    /**
     * @param T $item
     *
     * Check if the list contains the given value
     */
    public function contains(Item $item): bool;

    /**
     * Check if there is an element in the list for which the given predicate returns true.
     *
     * @param callable(T): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * @param T $item
     *
     * Get the index of the first occurrence of the given item in the list or null if the list does not contain the item
     */
    public function indexOf(Item $item): ?int;

    /**
     * @param T $item
     *
     * Count the number of occurrences of the given value in the list
     */
    public function countOccurrences(Item $item): int;

    /**
     * @param T $item
     */
    public function add(Item $item): void;

    /**
     * @param T $item
     *
     * @return bool Whether the item was removed
     *
     * Remove first occurrence of the given item from the list
     */
    public function removeFirst(Item $item): bool;

    /**
     * @param T $item
     *
     * Remove all occurrences of the given item from the list and return the number of removed elements
     */
    public function removeAll(Item $item): int;

    /**
     * Delete all elements from the list.
     */
    public function clear(): void;

    /**
     * Execute the given callback for each element of the list.
     *
     * @param callable(T): void $callback
     */
    public function walk(callable $callback): void;

    /**
     * Filter the list by the given callback - delete all elements for which the callback returns false.
     *
     * @param callable(T): bool $callback
     */
    public function filter(callable $callback): void;
}
