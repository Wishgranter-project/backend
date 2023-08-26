<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Controller\ControllerBase;
use AdinanCenci\Player\Exception\NotFound;
use AdinanCenci\Player\Helper\JsonResource;

class ItemRead extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $uuid = $request->getAttribute('itemUuid');
        if (!$item = $this->playlistManager->getItemByUuid($uuid)) {
            throw new NotFound('Item ' . $playlistId . ' does not exist.');
        }

        $data = $this->describer->describe($item);

        $resource = new JsonResource();
        return $resource
            ->setData($data)
            ->renderResponse();
    }
}
