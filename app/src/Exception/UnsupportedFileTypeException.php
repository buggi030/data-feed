<?php

namespace App\Exception;

class UnsupportedFileTypeException extends AppException
{
    public function __construct($context)
    {
        parent::__construct('File type is not supported', $context);
    }
}
