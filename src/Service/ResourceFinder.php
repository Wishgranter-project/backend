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
                $serviceManager->get('sourceSliderKz'),
                $serviceManager->get('sourceYoutube')
            ]
        );
    }

    /**
     * @param string[] $parameters
     *
     * @return Resource[]
     */
    public function findResources(array $parameters) : array
    {
        foreach ($this->sources as $source) {
            if ($resources = $this->searchOnSource($source, $parameters)) {
                return $resources;
            }
        }

        return [];
    }

    /**
     * @param SourceInterface $source
     * @param string[] $parameters
     *
     * @return Resource[]
     */
    protected function searchOnSource(SourceInterface $source, array $parameters) : array
    {
        $resources = $source->search($parameters);

        usort($resources, $this->sortResources($parameters));

        return array_values($resources);
    }

    /**
     * If the artist or soundtrack is present in the title or description,
     * then sort the item higher.
     *
     * @param string[]
     *
     * @return callable
     */
    public function sortResources(array $parameters) 
    {
        $related = $parameters['soundtrack'] ?? $parameters['artist'] ?? '';

        if (! $related) {
            return function($a, $b) 
            {
                return 0;
            };
        }

        return function($r1, $r2) use($related) 
        {
            if ($r1->id == $r2->id) {
                return 0;
            }

            if (
                substr_count($r1->title, $related) &&
                substr_count($r2->title, $related)
            ) {
                return 0;
            }

            return substr_count($r1->title, $related)
                ? 1
                : -1;
        };
    }
}
