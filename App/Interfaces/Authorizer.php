<?php declare(strict_types=1);

namespace App\Interfaces;

use App\Abstracts\Transaction;
use App\Abstracts\User;

interface Authorizer
{
    public function getAuthorization(User $user = null, Transaction $transaction = null): bool;
}
