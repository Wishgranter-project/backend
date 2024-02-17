<?php

namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use AdinanCenci\AetherMusic\Aether;
use AdinanCenci\AetherMusic\Description;
use AdinanCenci\Player\Service\ServicesManager;
use AdinanCenci\Player\Service\Describer;
use AdinanCenci\Player\Helper\JsonResource;

class DiscoverResources extends ControllerBase
{
    /**
     * @var AdinanCenci\Player\Service\Describer
     */
    protected Describer $describer;

    /**
     * @param AdinanCenci\AetherMusic\Aether $aether
     * @param AdinanCenci\Player\Service\Describer $describer
     */
    public function __construct(Aether $aether, Describer $describer)
    {
        $this->aether    = $aether;
        $this->describer = $describer;
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new self(
            $servicesManager->get('aether'),
            $servicesManager->get('describer')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateResponse(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $resources = $this->find($request);

        $data = [];
        foreach ($resources as $resource) {
            $data[] = $this->describer->describe($resource);
        }

        $resource = new JsonResource();
        return $resource
            ->setData($data)
            ->renderResponse();

        return $response;
    }

    protected function find(ServerRequestInterface $request): array
    {
        $description = $this->buildDescription($request);
        $search      = $this->aether->search($description);

        $search->addDefaultCriteria();

        $resources = $search->find();
        return $resources;
    }

    protected function buildDescription(ServerRequestInterface $request): Description
    {
        return Description::createFromArray([
            'title'      => $request->get('title'),
            'artist'     => $request->get('artist'),
            'soundtrack' => $request->get('soundtrack')
        ]);
    }
}
