<?php

namespace WishgranterProject\Backend\Helper;

use Psr\Http\Message\ResponseInterface;
use WishgranterProject\Backend\Helper\JsonResource;

/**
 * Represents paged portion of search results.
 */
class SearchResults implements \ArrayAccess, \Iterator
{
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
     * @param int $currentPageCount
     *   How many items are in $items.
     * @param int $currentPage
     *   The page $items can be find in.
     * @param int $pagesCount
     *   How many pages of search results are there.
     * @param int $itemsPerPage
     *   The maximum number of items there should be in a page.
     * @param int $resultsCount
     *   How many results are there.
     */
    public function __construct(
        protected array $items,
        protected int $currentPageCount,
        protected int $currentPage,
        protected int $pagesCount,
        protected int $itemsPerPage,
        protected int $resultsCount
    ) {
        $this->items = array_values($items);
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

    /**
     * Renders the search results into a json resource object.
     *
     * @return WishgranterProject\Backend\Helper\JsonResource
     *   Json resource.
     */
    public function renderResource(): JsonResource
    {
        $resource = new JsonResource($this->items, 200);
        $resource->addMeta([
            'resultsCount'     => $this->resultsCount,
            'itemsPerPage'     => $this->itemsPerPage,
            'pagesCount'       => $this->pagesCount,
            'currentPage'      => $this->currentPage,
            'currentPageCount' => $this->currentPageCount,
        ]);

        return $resource;
    }

    /**
     * Renders the search results into a http response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   Response object.
     */
    public function renderResponse(): ResponseInterface
    {
        return $this->renderResource()->renderResponse();
    }
}
