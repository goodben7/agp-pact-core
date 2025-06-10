<?php

namespace App\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Random\RandomException;

class IdGenerator extends AbstractIdGenerator
{
    /**
     * @throws RandomException
     */
    public function generateId(EntityManagerInterface $em, object|null $entity): string
    {
        $currentDateTime = new \DateTime();
        $dateTimeString = $currentDateTime->format('mdHis');
        $randomLetters = $this->generateRandomLetters(4);
        return $entity::ID_PREFIX . strtoupper($randomLetters . $dateTimeString);
    }

    /**
     * @throws RandomException
     */
    protected function generateRandomLetters(int $length): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $randomLetters = '';
        for ($i = 0; $i < $length; $i++)
            $randomLetters .= $characters[random_int(0, strlen($characters) - 1)];

        return $randomLetters;
    }
}