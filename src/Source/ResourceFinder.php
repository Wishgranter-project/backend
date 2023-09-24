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

        $filter = new Filter($parameters);
        $resources = array_filter($resources, [$filter, 'filter']);

        usort($resources, $this->sortResources($parameters));

        return array_values($resources);
    }

    /**
     * Sort resources based on the music's description.
     *
     * @param string[]
     *
     * @return callable
     */
    public function sortResources(array $parameters) 
    {
        $sorter = new Sorter($parameters);
        return [$sorter, 'compare'];
    }

    public static function normalizeString(string $string) : string
    {
        $string = str_replace('the', '', $string);
        $string = str_replace(' ', '', $string);
        $string = strtolower($string);

        return $string;
    }
}
