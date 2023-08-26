<?php 
namespace AdinanCenci\Player\Helper;

use Psr\Http\Message\ResponseInterface;
use AdinanCenci\Psr17\ResponseFactory;

class JsonResource 
{
    protected $statusCode = 200;

    protected $errors    = [];
    protected $warnings  = [];
    protected $successes = [];
    protected $infos     = [];

    protected $meta      = [];

    protected $data      = null;

    public function renderResponse() : ResponseInterface
    {
        $factory = new ResponseFactory();
        $json = $this->renderJson();

        switch ($this->statusCode) {
            case 200:
                $response = $factory->ok($json);
                break;
            case 201:
                $response = $factory->created($json);
                break;
            case 400:
                $response = $factory->badRequest($json);
                break;
            case 401:
                $response = $factory->unauthorized($json);
                break;
            case 403:
                $response = $factory->forbidden($json);
                break;
            case 404:
                $response = $factory->notFound($json);
                break;
            case 500:
                $response = $factory->internalServerError($json);
                break;
            case 501:
                $response = $factory->notImplemented($json);
                break;
            case 502:
                $response = $factory->badGateway($json);
                break;
            case 503:
                $response = $factory->serviceUnavailable($json);
                break;
        }

        $response = $response->withAddedHeader('Content-Type', 'application/json');

        return $response;
    }

    public function setStatusCode(int $statusCode) 
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function setMeta(string $variable, $value) : JsonResource 
    {
        $this->meta[$variable] = $value;
        return $this;
    }

    public function setData($data) : JsonResource
    {
        $this->data = $data;
        return $this;
    }

    public function addError(int $code, string $title, ?string $detail = NULL) : JsonResource
    {
        $this->errors[] = $this->createMessage('error', $code, $title, $detail);
        return $this;
    }

    public function addSuccess(int $code, string $title, ?string $detail = NULL) : JsonResource
    {
        $this->successes[] = $this->createMessage('success', $code, $title, $detail);
        return $this;
    }

    public function addWarning(int $code, string $title, ?string $detail = NULL) : JsonResource
    {
        $this->warnings[] = $this->createMessage('warning', $code, $title, $detail);
        return $this;
    }

    public function addInfo(int $code, string $title, ?string $detail = NULL) : JsonResource
    {
        $this->infos[] = $this->createMessage('info', $code, $title, $detail);
        return $this;
    }

    protected function createMessage(string $type, int $code, string $title, ?string $detail = null) : array
    {
        $message = [
            'type'  => $type,
            'code'  => $code,
            'title' => $title,
        ];

        if ($detail) {
            $message['detail'] = $detail;
        }

        return $message;
    }

    protected function renderJson() : string 
    {
        $array = [];

        if ($this->errors) {
            $array['errors'] = $this->errors;
        }

        if ($this->warnings) {
            $array['warnings'] = $this->warnings;
        }

        if ($this->successes) {
            $array['successes'] = $this->successes;
        }

        if ($this->infos) {
            $array['infos'] = $this->infos;
        }

        if ($this->meta) {
            $array['meta'] = $this->meta;
        }

        if (!is_null($this->data)) {
            $array['data'] = $this->data;
        }

        return json_encode($array);
    }
}
