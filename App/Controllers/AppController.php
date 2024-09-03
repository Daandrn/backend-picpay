<?php

namespace App\Controllers;

use App\DTO\TransferDTO;
use App\Interfaces\Authorizer;
use App\Interfaces\Mailer;
use App\Microservices\DevToolsAuthorizer;
use App\Microservices\UserMailer;
use App\Requests\{ApiResponse, TransferRequest};
use App\Services\TransferService;
use DataBase\AppInstall;

require_once __DIR__ . "/../Requests/Request.php";
require_once __DIR__ . "/../Requests/ApiResponse.php";
require_once __DIR__ . "/../../DataBase/DataBase.php";
require_once __DIR__ . "/../Services/TransferService.php";
require_once __DIR__ . '/../DTO/TransferDTO.php';
require_once __DIR__ . '/../Microservices/DevToolsAuthorizer.php';
require_once __DIR__ . '/../Microservices/UserMailer.php';
require_once __DIR__ . '/../Requests/CurlFuncs.php';
require_once __DIR__ . '/../Requests/TransferRequest.php';

class AppController
{
    protected TransferService $transferService;
    protected Authorizer $authorizer;
    protected Mailer $mailer;
    protected TransferRequest $transferRequest;

    public function __construct()
    {
        $this->transferService = new TransferService;
        $this->authorizer = new DevToolsAuthorizer;
        $this->mailer = new UserMailer;
        $this->transferRequest = new TransferRequest;
    }

    public function transfer(): ApiResponse
    {
        $request = $this->transferRequest->post1();

        $response = $this->transferService->makeTransfer(
            TransferDTO::make($request),
            authorizer: $this->authorizer,
            mailer: $this->mailer,
        );

        return ApiResponse::send(
            $response['body'],
            $response['status']['code']
        );
    }

    // public function getAccountBalance(): ApiResponse
    // {
    //     # code...
    // }

    public function appinstall(): void
    {
        require __DIR__ . '/../../DataBase/AppInstall.php';

        (new AppInstall)->exec();

        echo json_encode([
            'message' => "Instalação realizada!"
        ]);

        return;
    }
}
