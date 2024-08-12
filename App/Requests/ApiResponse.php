<?php

namespace App\Requests;

class ApiResponse
{
    protected function __construct(array $body = [], int $httpResponseCode = 200)
    {
        http_response_code($httpResponseCode);

        echo json_encode([
            'reponse' => $body
        ]);
    }

    public static function send(array $body = [], int $httpResponseCode = 200): self
    {
        return new self($body, $httpResponseCode);
    }
}