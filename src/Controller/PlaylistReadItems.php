<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\DescriptivePlaylist\Playlist;
use AdinanCenci\Player\Helper\JsonResource;
use AdinanCenci\Player\Exception\NotFound;

class PlaylistReadItems extends ControllerBase 
{
    use PaginationTrait;

    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $playlistId = $request->getAttribute('playlist');
        if (! $this->playlistManager->playlistExists($playlistId)) {
            throw new NotFound('Playlist ' . $playlistId . ' does not exist.');
        }

        list($page, $itensPerPage, $offset, $limit) = $this->getPaginationInfo($request);

        $playlist      = $this->playlistManager->getPlaylist($playlistId);
        $total         = $playlist->lineCount - 1;
        $pages         = $this->numberPages($total, $itensPerPage);

        $list          = $playlist->getItems(range($offset, $offset + $limit - 1));
        $count         = count($list);

        foreach ($list as $item) {
            $data[] = $this->describer->describe($item);
        }

        $resource = new JsonResource();
        return $resource
            ->setMeta('total', $total)
            ->setMeta('itensPerPage', $itensPerPage)
            ->setMeta('pages', $pages)
            ->setMeta('page', $page)
            ->setMeta('count', $count)
            ->setData($data)
            ->renderResponse();
    }
}
