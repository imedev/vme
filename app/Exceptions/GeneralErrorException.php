<?php

namespace App\Exceptions;

use App\Exceptions\Exception;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class GeneralErrorException extends Exception
{
    protected $code = SymfonyResponse::HTTP_BAD_REQUEST;
    protected $message = 'General Error.';
}
