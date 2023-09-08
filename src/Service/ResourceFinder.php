<?php
namespace AdinanCenci\Player\Service;

use AdinanCenci\Player\Service\SourceInterface;

class ResourceFinder 
{
    protected array $sources;

    public function __construct(array $sources) 
    {
        $this->sources = $sources;
    }

    public static function create() : ResourceFinder
    {
        $serviceManager = ServicesManager::singleton();

        return new self(
            [
                $serviceManager->get('sourceYoutube')
            ]
        );
    }

    public function findResources(array $parameters) : array
    {
        foreach ($this->sources as $source) {
            if ($resources = $this->searchOnSource($source, $parameters)) {
                return $resources;
            }
        }

        return [];
    }

    protected function searchOnSource(SourceInterface $source, array $parameters) : array
    {
        $resources = $source->search($parameters);

        usort($resources, $this->sortResources($parameters));

        return array_values($resources);
    }

    public function sortResources(array $parameters) 
    {
        $related = $parameters['artist'] ?? $parameters['soundtrack'] ?? '';

        if (! $related) {
            return function($a, $b) 
            {
                return 0;
            };
        }

        return function($a, $b) use($related) 
        {
            if ($a['id'] == $b['id']) {
                return 0;
            }

            return substr_count($a['title'], $related)
                ? 1
                : -1;
        };
    }
}
