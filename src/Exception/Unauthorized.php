<?php

namespace WishgranterProject\Backend\Exception;

class Unauthorized extends \Exception
{
    public function __construct(
        string $message = 'You are unauthorized to access this resource.',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
