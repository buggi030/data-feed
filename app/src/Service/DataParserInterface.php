<?php

namespace App\Service;

use App\Entity\Coffee;
use App\Exception\AppException;

interface DataParserInterface
{
    /**
     * @return Coffee[] of deserialized Coffee objects
     *
     * @throws AppException
     */
    public function parse(): array;
}
