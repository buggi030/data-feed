<?php

namespace App\Exception;

class FileNotFoundException extends AppException
{
    public function __construct($context)
    {
        parent::__construct('File not found', $context);
    }
}
