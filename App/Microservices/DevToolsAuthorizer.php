<?php declare(strict_types=1);

namespace App\Microservices;

use App\Abstracts\Transaction;
use App\Abstracts\User;
use App\Interfaces\Authorizer;
use App\Requests\Request;

class DevToolsAuthorizer implements Authorizer
{
    public function getAuthorization(User $user = null, Transaction $transaction = null): bool
    {
        $url = 'https://util.devi.tools/api/v2/authorize';
        
        $response = Request::sendGet($url);
        
        $authorization = json_validate($response) ? json_decode($response, false) : null;
        
        if (
            isset($authorization->data->authorization)
            && is_bool($authorization->data->authorization)
        ) {
            return $authorization->data->authorization;
        }

        return false;
    }
}
