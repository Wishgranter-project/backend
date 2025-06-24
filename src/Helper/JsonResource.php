<?php

namespace WishgranterProject\Backend\Helper;

use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Helper\SearchResults;
use Psr\Http\Message\ResponseInterface;
use AdinanCenci\Psr17\ResponseFactory;

/**
 * Represents a json resource.
 *
 * Helpfull to compose a json response.
 */
class JsonResource
{
    /**
     * The actual data of the response.
     *
     * @var array
     */
    protected $data       = null;

    /**
     * Meta information on the data.
     *
     * @var array
     */
    protected $meta       = [];

    /**
     * The HTTP status code.
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Error messages.
     *
     * @var array
     */
    protected $errors     = [];

    /**
     * Warning messages.
     *
     * @var array
     */
    protected $warnings   = [];

    /**
     * Success messages.
     *
     * @var array
     */
    protected $successes  = [];

    /**
     * Info messages.
     *
     * @var array
     */
    protected $infos      = [];

    /**
     * Constructor.
     *
     * @param mixed $data
     *   The data.
     * @param int $statusCode
     *   The HTTP status code.
     */
    public function __construct($data = null, int $statusCode = 200)
    {
        $this->data       = $data;
        $this->statusCode = $statusCode;
    }

    /**
     * Renders the resource into a response object.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   A HTTP response object.
     */
    public function renderResponse(): ResponseInterface
    {
        $json = $this->renderJson();

        $response = $this->instantiateResponse($this->statusCode, $json);
        $response = $response->withAddedHeader('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Instantiates a response object.
     *
     * Appropriate for the given status code.
     *
     * @param int $statusCode
     *   The HTTP status code.
     * @param string $json
     *   The body of the response.
     *
     * @return Psr\Http\Message\ResponseInterface
     *   A HTTP response object.
     */
    public function instantiateResponse(int $statusCode, string $json): ResponseInterface
    {
        $factory = new ResponseFactory();

        switch ($statusCode) {
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

        return $response;
    }

    /**
     * Sets the status code of the response.
     *
     * @param int $statusCode
     *   The HTTP status code.
     *
     * @return self
     *   Return itself.
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Sets a piece of meta data.
     *
     * @param int $variable
     *   Name of the metadata property.
     * @param mixed $value
     *   The value of the metadata.
     *
     * @return self
     *   Return itself.
     */
    public function setMeta(string $variable, $value): JsonResource
    {
        $this->meta[$variable] = $value;
        return $this;
    }

    /**
     * Sets the data.
     *
     * @param mixed $data
     *   The data.
     *
     * @return self
     *   Return itself.
     */
    public function setData($data): JsonResource
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Adds an error message.
     *
     * @param int $code
     *   Code for the message.
     * @param string $title
     *   Title for the message.
     * @param string|null $datail
     *   Details for the message.
     *
     * @return self
     *   Return itself.
     */
    public function addError(int $code, string $title, ?string $detail = null): JsonResource
    {
        $this->errors[] = $this->createMessage('error', $code, $title, $detail);
        return $this;
    }

    /**
     * Adds a success message.
     *
     * @param int $code
     *   Code for the message.
     * @param string $title
     *   Title for the message.
     * @param string|null $datail
     *   Details for the message.
     *
     * @return self
     *   Return itself.
     */
    public function addSuccess(int $code, string $title, ?string $detail = null): JsonResource
    {
        $this->successes[] = $this->createMessage('success', $code, $title, $detail);
        return $this;
    }

    /**
     * Adds a warning message.
     *
     * @param int $code
     *   Code for the message.
     * @param string $title
     *   Title for the message.
     * @param string|null $datail
     *   Details for the message.
     *
     * @return self
     *   Return itself.
     */
    public function addWarning(int $code, string $title, ?string $detail = null): JsonResource
    {
        $this->warnings[] = $this->createMessage('warning', $code, $title, $detail);
        return $this;
    }

    /**
     * Adds an info message.
     *
     * @param int $code
     *   Code for the message.
     * @param string $title
     *   Title for the message.
     * @param string|null $datail
     *   Details for the message.
     *
     * @return self
     *   Return itself.
     */
    public function addInfo(int $code, string $title, ?string $detail = null): JsonResource
    {
        $this->infos[] = $this->createMessage('info', $code, $title, $detail);
        return $this;
    }

    /**
     * Builds a message array.
     *
     * @param string $type
     *   The type of the message.
     * @param int $code
     *   The code of the message.
     * @param string $title
     *   The title of the message.
     * @param string|null $detail
     *   Further details.
     *
     * @return array
     *   The message structured as an array.
     */
    protected function createMessage(string $type, int $code, string $title, ?string $detail = null): array
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

    /**
     * Renders the object as a json string.
     *
     * @return string
     *   The body of the response.
     */
    protected function renderJson(): string
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


    public static function fromSearchResults(SearchResults $searchResult): JsonResource
    {
        $describer = ServicesManager::singleton()->get('describer');

        $data = $describer->describeAll($searchResult->items);
        $resource = new JsonResource($data, 200);

        $resource
            ->setMeta('total', $searchResult->total)
            ->setMeta('itemsPerPage', $searchResult->itemsPerPage)
            ->setMeta('pages', $searchResult->pages)
            ->setMeta('page', $searchResult->page)
            ->setMeta('count', $searchResult->count);

        return $resource;
    }
}
