<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\DescriptivePlaylist\Playlist;
use AdinanCenci\Player\Helper\JsonResource;

class PlaylistReadList extends ControllerBase 
{
    use PaginationTrait;

    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        list($page, $itensPerPage, $offset, $limit) = $this->getPaginationInfo($request);

        $all   = $this->playlistManager->getAllPlaylists();
        $total = count($all);
        $pages = $this->numberPages($total, $itensPerPage);
        $list  = array_slice($all, $offset, $limit);
        $count = count($list);

        $data  = [];
        foreach ($list as $playlistId => $playlist) {
            $data[] = $this->describer->describe($playlist);
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