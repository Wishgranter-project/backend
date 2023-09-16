<?php 
namespace AdinanCenci\Player\Helper;

use AdinanCenci\Player\Service\ServicesManager;

class SearchResults 
{
    protected array $items;

    protected int $count;
    protected int $page;
    protected int $pages;
    protected int $itensPerPage;
    protected int $total;

    public function __construct(array $items, int $count, int $page, int $pages, int $itensPerPage, int $total) 
    {
        $this->items        = $items;
        $this->count        = $count;
        $this->page         = $page;
        $this->pages        = $pages;
        $this->itensPerPage = $itensPerPage;
        $this->total        = $total;
    }

    public function __get($var) 
    {
        return $this->{$var};
    }

    public function getJsonResource() : JsonResource
    {
        $describer = ServicesManager::singleton()->get('describer');

        $resource = new JsonResource();

        $data = [];
        foreach ($this->items as $item) {
            $data[] = $describer->describe($item);
        }

        $resource
            ->setStatusCode(200)
            ->setData($data)
            ->setMeta('total', $this->total)
            ->setMeta('itensPerPage', $this->itensPerPage)
            ->setMeta('pages', $this->pages)
            ->setMeta('page', $this->page)
            ->setMeta('count', $this->count);

        return $resource;
    }

    public static function empty() : SearchResults
    {
        return new self([], 0, 0, 0, 0, 0);
    }
}
