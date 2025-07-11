<?php

namespace WishgranterProject\Backend\Helper;

/**
 * Represents paged portion of search results.
 */
class SearchResults implements \ArrayAccess, \Iterator
{
    /**
     * The actual results.
     *
     * @var array
     */
    protected array $items;

    /**
     * How many items are in $items.
     *
     * @var int
     */
    protected int $count;

    /**
     * The page $items can be find in.
     *
     * @var int
     */
    protected int $page;

    /**
     * How many pages of search results are there.
     *
     * @var int
     */
    protected int $pages;

    /**
     * The maximum number of items there should be in a page.
     *
     * @var int
     */
    protected int $itemsPerPage;

    /**
     * How many results are there.
     *
     * @var int
     */
    protected int $total;

    /**
     * Internal pointer.
     *
     * To keep track of during iterations.
     *
     * @var int
     */
    protected int $pointer;

    /**
     * Constructor.
     *
     * @param array $items
     *   The actual results.
     * @param int $count
     *   How many items are in $items.
     * @param int $page
     *   The page $items can be find in.
     * @param int $pages
     *   How many pages of search results are there.
     * @param int $itemsPerPage
     *   The maximum number of items there should be in a page.
     * @param int $total
     *   How many results are there.
     */
    public function __construct(
        array $items,
        int $count,
        int $page,
        int $pages,
        int $itemsPerPage,
        int $total
    ) {
        $this->items        = $items;
        $this->count        = $count;
        $this->page         = $page;
        $this->pages        = $pages;
        $this->itemsPerPage = $itemsPerPage;
        $this->total        = $total;
    }

    public function __get($var)
    {
        return $this->{$var};
    }

    /**
     * Returns a new empty search results object.
     *
     * @return WishgranterProject\Backend\Helper\SearchResults
     *   New search results object.
     */
    public static function empty(): SearchResults
    {
        return new self([], 0, 0, 0, 0, 0);
    }

    /**
     * ArrayAccess::offsetExists()
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * ArrayAccess::offsetGet()
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }

    /**
     * ArrayAccess::offsetSet()
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->items[$offset] = $value;
    }

    /**
     * ArrayAccess::offsetUnset()
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * Iterator::current()
     */
    public function current(): mixed
    {
        return $this->items[$this->pointer];
    }

    /**
     * Iterator::key()
     */
    public function key(): mixed
    {
        return $this->pointer;
    }

    /**
     * Iterator::next()
     */
    public function next(): void
    {
        $this->pointer++;
    }

    /**
     * Iterator::rewind()
     */
    public function rewind(): void
    {
        $this->pointer = 0;
    }

    /**
     * Iterator::valid()
     */
    public function valid(): bool
    {
        return isset($this->items[$this->pointer]);
    }
}
