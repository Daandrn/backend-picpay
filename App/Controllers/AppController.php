<?php

namespace App\Controllers;

use App\DTO\TransferDTO;
use App\Requests\ApiResponse;
use App\Requests\HttpRequest;
use App\Services\TransferService;
use DataBase\InstalarBd;

require_once __DIR__ . "/../Requests/HttpRequests.php";
require_once __DIR__ . "/../Requests/ApiResponse.php";
require_once __DIR__ . "/../../DataBase/DataBase.php";
require_once __DIR__ . "/../Services/TransferService.php";
require_once __DIR__ . '/../DTO/TransferDTO.php';

class AppController
{
    protected TransferService $transferService;

    public function __construct()
    {
        $this->transferService = new TransferService;
    }

    public function transfer(): ApiResponse
    {
        $request = (new HttpRequest)->post();

        $response = $this->transferService->fazerTransferencia(
            TransferDTO::make($request)
        );

        return ApiResponse::send(
            $response['body'],
            $response['status']['code']
        );
    }

    public function instalarApp(): void
    {
        require __DIR__ . '/../../DataBase/instalarApp.php';

        (new InstalarBd)->exec();

        echo json_encode([
            'message' => "Instalação realizada!"
        ]);

        return;
    }
}