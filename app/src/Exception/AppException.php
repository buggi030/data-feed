<?php

namespace App\Exception;

class AppException extends \Exception
{
    private array $context;

    public function __construct(string $message, array $context = [])
    {
        parent::__construct($message);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
