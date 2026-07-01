<?php

namespace WishgranterProject\Backend\Exception;

class NotFound extends \Exception
{
    public function __construct(
        string $message = 'The resource you are looking for cound not be located.',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
