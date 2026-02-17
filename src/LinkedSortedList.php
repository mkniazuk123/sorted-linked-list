<?php

namespace MKniazuk\SortedLinkedList;

/**
 * @template T of mixed
 *
 * @implements SortedList<T>
 *
 * @internal
 */
class LinkedSortedList implements SortedList
{
    /** @var null|Node<T> */
    private ?Node $head = null;

    /**
     * @param iterable<T>       $values
     * @param \Closure(T,T):int $comparator
     */
    public function __construct(
        iterable $values,
        private readonly \Closure $comparator
    ) {
        foreach ($values as $value) {
            $this->add($value);
        }
    }

    public function __clone()
    {
        if (null === $this->head) {
            return;
        }

        $old = $this->head;
        $this->head = null;
        $new = null;

        do {
            $node = new Node($old->item);
            if (null === $new) {
                $this->head = $node;
            } else {
                $new->next = $node;
            }
            $new = $node;

            $old = $old->next;
        } while (null !== $old);
    }

    public function count(): int
    {
        $size = 0;
        foreach ($this->iterateNodes() as $ignored) {
            ++$size;
        }

        return $size;
    }

    public function isEmpty(): bool
    {
        return null === $this->head;
    }

    public function toArray(): array
    {
        $array = [];
        foreach ($this->iterateNodes() as $node) {
            $array[] = $node->item;
        }

        return $array;
    }

    /**
     * Get the first element of the list or throw an exception if the list is empty.
     *
     * @throws NoSuchElementException
     */
    public function head(): mixed
    {
        return $this->head->item ?? throw new NoSuchElementException();
    }

    /**
     * Get the last element of the list or throw an exception if the list is empty.
     *
     * @throws NoSuchElementException
     */
    public function tail(): mixed
    {
        $lastNode = null;
        foreach ($this->iterateNodes() as $node) {
            $lastNode = $node;
        }

        return $lastNode->item ?? throw new NoSuchElementException();
    }

    public function contains(mixed $item): bool
    {
        foreach ($this->iterateNodes() as $node) {
            if (0 === ($this->comparator)($node->item, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param callable(T): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        foreach ($this->iterateNodes() as $node) {
            if ($predicate($node->item)) {
                return true;
            }
        }

        return false;
    }

    public function indexOf(mixed $item): ?int
    {
        $index = 0;
        foreach ($this->iterateNodes() as $node) {
            if (0 === ($this->comparator)($node->item, $item)) {
                return $index;
            }
            ++$index;
        }

        return null;
    }

    public function countOccurrences(mixed $item): int
    {
        $count = 0;
        foreach ($this->iterateNodes() as $node) {
            if (0 === ($this->comparator)($node->item, $item)) {
                ++$count;
            }
        }

        return $count;
    }

    public function add(mixed $item): void
    {
        $current = null;
        $previous = null;
        foreach ($this->iterateNodes() as $current) {
            if (($this->comparator)($current->item, $item) > 0) {
                $newNode = new Node($item, next: $current);
                if (null === $previous) {
                    $this->head = $newNode;
                } else {
                    $previous->next = $newNode;
                }

                return;
            }

            $previous = $current;
        }

        if (null === $current) {
            $pointer = &$this->head;
        } else {
            $pointer = &$current->next;
        }
        $pointer = new Node($item);
    }

    public function removeFirst(mixed $item): bool
    {
        return 1 === $this->removeItems($item, limit: 1);
    }

    public function removeAll(mixed $item): int
    {
        return $this->removeItems($item);
    }

    public function clear(): void
    {
        $this->head = null;
    }

    /**
     * @param callable(T): void $callback
     */
    public function walk(callable $callback): void
    {
        foreach ($this->iterateNodes() as $node) {
            $callback($node->item);
        }
    }

    /**
     * @param callable(T): bool $callback
     */
    public function filter(callable $callback): void
    {
        $previous = null;
        foreach ($this->iterateNodes() as $current) {
            if (!$callback($current->item)) {
                if (null === $previous) {
                    $this->head = $current->next;
                } else {
                    $previous->next = $current->next;
                }
            } else {
                $previous = $current;
            }
        }
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->iterateNodes() as $node) {
            yield $node->item;
        }
    }

    /**
     * @return \Generator<Node<T>>
     */
    private function iterateNodes(): \Generator
    {
        $current = $this->head;
        while (null !== $current) {
            yield $current;
            $current = $current->next;
        }
    }

    private function removeItems(mixed $item, ?int $limit = null): int
    {
        $removed = 0;
        $previous = null;
        foreach ($this->iterateNodes() as $current) {
            $comparison = ($this->comparator)($current->item, $item);
            if (0 === $comparison) {
                if (null === $previous) {
                    $this->head = $current->next;
                } else {
                    $previous->next = $current->next;
                }
                ++$removed;

                if (null !== $limit && $removed >= $limit) {
                    break;
                }
            } elseif ($comparison > 0) {
                // Since the list is sorted, we can stop searching once we find an element greater than the target.
                break;
            } else {
                $previous = $current;
            }
        }

        return $removed;
    }
}
