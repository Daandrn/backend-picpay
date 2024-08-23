<?php declare(strict_types=1);

namespace App\Interfaces;

interface Mailer
{
    public function send(int $userId, string $body): void;
}