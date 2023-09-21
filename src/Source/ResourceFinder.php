<?php
namespace AdinanCenci\Player\Source;

use AdinanCenci\Player\Service\ServicesManager;

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
                'sliderKz' => $serviceManager->get('sourceSliderKz'),
                'youtube' => $serviceManager->get('sourceYoutube'),
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

        $related = is_array($related) ? $related[0] : $related;

        if (! $related) {
            return function($a, $b) 
            {
                return 0;
            };
        }

        return function($resource1, $resource2) use($related) 
        {
            if ($resource1->id == $resource2->id) {
                return 0;
            }

            $related = ResourceFinder::normalizeString($related);
            $title1  = ResourceFinder::normalizeString($resource1->title);
            $title2  = ResourceFinder::normalizeString($resource2->title);

            if (
                substr_count($title1, $related) &&
                substr_count($title2, $related)
            ) {
                return 0;
            }

            $count = substr_count($title1, $related);

            return $count > 0
                ? -1
                :  1;
        };
    }

    public static function normalizeString(string $string) 
    {
        $string = str_replace('the', '', $string);
        $string = str_replace(' ', '', $string);
        $string = strtolower($string);

        return $string;
    }
}
