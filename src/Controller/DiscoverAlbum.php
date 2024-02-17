<?php

namespace AdinanCenci\Player\Controller;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use AdinanCenci\Discography\Source\SourceMusicBrainz;
use AdinanCenci\Player\Service\Describer;
use AdinanCenci\Player\Service\ServicesManager;
use AdinanCenci\Player\Helper\JsonResource;

class DiscoverAlbum extends ControllerBase
{
    /**
     * @var AdinanCenci\Discography\Source\SourceMusicBrainz
     */
    protected SourceMusicBrainz $discography;

    /**
     * @var AdinanCenci\Player\Service\Describer
     */
    protected Describer $describer;

    /**
     * @param AdinanCenci\Discography\Source\SourceMusicBrainz $discography
     * @param AdinanCenci\Player\Service\Describer $describer
     */
    public function __construct(SourceMusicBrainz $discography, Describer $describer)
    {
        $this->discography = $discography;
        $this->describer   = $describer;
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new self(
            $servicesManager->get('discographyMusicBrainz'),
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

        $artistName = $request->get('artist');
        $albumTitle = $request->get('title');
        $album      = $this->discography->getAlbum($artistName, $albumTitle);
        $data       = $this->describer->describe($album);

        $resource = new JsonResource();

        return $resource
            ->setStatusCode(200)
            ->setData($data)
            ->renderResponse();
    }
}
