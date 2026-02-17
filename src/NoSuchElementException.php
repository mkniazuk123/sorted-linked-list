<?php

namespace MKniazuk\SortedLinkedList;

final class NoSuchElementException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct();
    }
}
