<?php declare(strict_types=1);

namespace App\Microservices;

use App\Interfaces\Mailer;
use App\Requests\Request;

require_once __DIR__ . '/../Interfaces/Mailer.php';

class UserMailer implements Mailer
{
    public function send(int $userId, string $message): void
    {
        $maxAttemps = 0;
        
        do {
            $statusSend = Request::sendPost('https://util.devi.tools/api/v1/notify', ['message' => $message]);
            
            if (
                is_string($statusSend)
                && strlen($statusSend) === 0
            ) {
                error_log('tentativa ' . strval($maxAttemps) . ' - Email enviado!');error_get_last();
                
                break;
            }
            
            $response = json_validate($statusSend) ? json_decode($statusSend, false) : null;

            error_log('tentativa ' . strval($maxAttemps) . ' - Erro ao enviar e-mail!');error_get_last();
            sleep(1);
            $maxAttemps++;
        } while (
            (!isset($response->status)
            || $response->status === 'error')
            && $maxAttemps < 5
        );

        return;
    }
}