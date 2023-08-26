<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\DescriptivePlaylist\Playlist;

use AdinanCenci\Player\Exception\NotFound;
use AdinanCenci\Player\Helper\JsonResource;

class PlaylistUpdate extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $playlistId = $request->getAttribute('playlist');

        $playlist = $this->playlistManager->getPlaylist($playlistId);
        if (! $playlist) {
            throw new NotFound('Playlist ' . $playlistName . ' not found.');
        }

        $header   = $playlist->getHeader();
        $header->empty();

        $postData = $this->getPostData($request);

        foreach ($postData as $k => $v) {
            if ($header->isValidPropertyName($k)) {
                $header->{$k} = $v;
            } else {
                throw new \InvalidArgumentException('Unrecognized property ' . $k);
            }
        }

        $playlist->setHeader($header);

        $resource = new JsonResource();
        $data = $this->describer->describe($playlist);

        return $resource
            ->setStatusCode(200)
            ->addSuccess(200, 'Changes saved')
            ->setData($data)
            ->renderResponse();
    }
}
