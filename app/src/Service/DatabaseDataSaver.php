<?php

namespace App\Service;

use App\Exception\DataSavingException;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseDataSaver implements DataSaverInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(array $objects): int
    {
        foreach ($objects as $coffee) {
            $this->entityManager->persist($coffee);
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new DataSavingException('Save data error: '.$e->getMessage(), []);
        }

        return count($objects);
    }
}
