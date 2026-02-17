# sorted-linked-list

## Usage


Creation:
```php
$list = SortedListFactory::ofIntegers([1, 2, 3]);
```

```php
$list = SortedListFactory::ofStrings(['banana', 'apple', 'cherry']);
```

```php
$list = SortedListFactory::create(
    [new CustomObject(3), new CustomObject(1), new CustomObject(2)],
    fn($a, $b) => $a->getValue() <=> $b->getValue()
);
```

Reading:
```php
$list = SortedListFactory::ofIntegers([5, 7, 5, 2, 5]);
$list->toArray(); // [2, 5, 5, 5, 7]
count($list); // 5

foreach ($list as $value) {
    echo $value . ' ';
}
// Output: 2 5 5 5 7

$list->head(); // 2
$list->tail(); // 7

$list->contains(5); // true
$list->contains(3); // false

$list->exists(fn($x) => $x > 6); // true
$list->exists(fn($x) => $x < 2); // false

$list->indexOf(5); // 1
$list->indexOf(3); // null

$list->countOccurrences(5); // 3
$list->countOccurrences(3); // 0

$list->walk(fn($x) => echo $x . ' ');
// Output: 2 5 5 5 7
```

Modifying:
```php
$list->add(1);
$list->toArray(); // [1, 2, 5, 5, 5, 7]

$list->removeFirst(5);
$list->toArray(); // [1, 2, 5, 5, 7]

$list->removeAll(5);
$list->toArray(); // [1, 2, 7]

$list->filter(fn($x) => $x > 1);
$list->toArray(); // [2, 7]

$list->clear();
$list->toArray(); // []
```

Cloning:
```php
$list1 = SortedListFactory::ofIntegers([1, 2, 3]);
$list2 = clone $list1;

$list2->add(4);

$list1->toArray(); // [1, 2, 3]
$list2->toArray(); // [1, 2, 3, 4]
```