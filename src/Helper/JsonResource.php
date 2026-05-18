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
     * The HTTP status code.
     *
     * @var int
     */
    protected int $statusCode = 200;

    /**
     * Meta information on the data.
     *
     * @var array
     */
    protected \stdClass $meta;

    /**
     * Messages.
     *
     * @var array
     */
    protected array $messages = [];

    /**
     * The actual data of the response.
     *
     * @var mixed
     */
    protected \stdClass $data;

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
        $this->data       = (object) $data;
        $this->statusCode = $statusCode;
        $this->meta       = new \stdClass();
        $this->setMeta('statusCode', $statusCode);
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
        $this->setMeta('statusCode', $statusCode);
        return $this;
    }

    /**
     * Sets the metadata.
     *
     * @param \stdClass $metaData
     *   Metadata
     *
     * @return self
     *   Return itself.
     */
    public function setMetaData(\stdClass $metaData): JsonResource
    {
        $this->meta = $metaData;
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
        $this->meta->{$variable} = $value;
        return $this;
    }

    /**
     * Sets multiple pieces of meta data.
     *
     * @param int $meta
     *   Associative array with the data to set.
     *
     * @return self
     *   Return itself.
     */
    public function addMeta(array $meta): JsonResource
    {
        foreach ($meta as $variable => $value) {
            $this->setMeta($variable, $value);
        }

        return $this;
    }

    /**
     * Sets a piece of data.
     *
     * @param int $variable
     *   Name of the data property.
     * @param mixed $value
     *   The value of the data.
     *
     * @return self
     *   Return itself.
     */
    public function setData(string $variable, $value): JsonResource
    {
        $this->data->{$variable} = $value;
        return $this;
    }

    /**
     * Sets multiple pieces of data.
     *
     * @param int $data
     *   Associative array with the data to set.
     *
     * @return self
     *   Return itself.
     */
    public function addData(array $data): JsonResource
    {
        foreach ($data as $variable => $value) {
            $this->setData($variable, $value);
        }

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
    public function setDataBody($data): JsonResource
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
     * @param string|null $description
     *   Details for the message.
     *
     * @return self
     *   Return itself.
     */
    public function addError(int $code, string $title, ?string $description = null): JsonResource
    {
        return $this->addMessage('error', $code, $title, $description);
    }

    /**
     * Adds a success message.
     *
     * @param int $code
     *   Code for the message.
     * @param string $title
     *   Title for the message.
     * @param string|null $description
     *   Details for the message.
     *
     * @return self
     *   Return itself.
     */
    public function addSuccess(int $code, string $title, ?string $description = null): JsonResource
    {
        return $this->addMessage('success', $code, $title, $description);
    }

    /**
     * Adds a warning message.
     *
     * @param int $code
     *   Code for the message.
     * @param string $title
     *   Title for the message.
     * @param string|null $description
     *   Details for the message.
     *
     * @return self
     *   Return itself.
     */
    public function addWarning(int $code, string $title, ?string $description = null): JsonResource
    {
        return $this->addMessage('warning', $code, $title, $description);
    }

    /**
     * Adds an info message.
     *
     * @param int $code
     *   Code for the message.
     * @param string $title
     *   Title for the message.
     * @param string|null $description
     *   Details for the message.
     *
     * @return self
     *   Return itself.
     */
    public function addInfo(int $code, string $title, ?string $description = null): JsonResource
    {
        return $this->addMessage('info', $code, $title, $description);
    }

    /**
     * Adds a message.
     *
     * @param string $type
     *   Type of the message.
     * @param int $code
     *   Code for the message.
     * @param string $title
     *   Title for the message.
     * @param string|null $description
     *   Details for the message.
     *
     * @return self
     *   Return itself.
     */
    public function addMessage(string $type, int $code, string $title, ?string $description = null): JsonResource
    {
        $this->messages[] = $this->createMessage($type, $code, $title, $description);
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
     * @param string|null $description
     *   Further details.
     *
     * @return array
     *   The message structured as an array.
     */
    protected function createMessage(string $type, int $code, string $title, ?string $description = null): array
    {
        $message = [
            'code'  => $code,
            'type'  => $type,
            'title' => $title,
        ];

        if ($description) {
            $message['description'] = $description;
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

        if ($this->messages) {
            $array['messages'] = $this->messages;
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
