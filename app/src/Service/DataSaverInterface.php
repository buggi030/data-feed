<?php

namespace App\Service;

use App\Exception\AppException;

interface DataSaverInterface
{
    /**
     * @return int count of saved objects
     *
     * @throws AppException
     */
    public function save(array $objects): int;
}
