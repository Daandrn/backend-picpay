<?php declare(strict_types=1);

namespace App\Requests;

use CurlHandle;
use Exception;

class CurlFuncs
{
    private CurlHandle $cUrl;
    
    public function init(): void
    {
        $cUrlInit = curl_init();

        if ($cUrlInit !== false) {
            $this->cUrl = $cUrlInit;

            return;
        }

        throw new Exception("Erro ao realizar requisição, verifique! Codigo do erro: 102", 500);
    }

    public function options(
        string $url,  
        bool $post = false,
        bool $returnTransfer = true, 
        bool $sslVerify = false,
        array $body = [],
        
    ): true {
        $array = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => $returnTransfer,
            CURLOPT_SSL_VERIFYPEER => $sslVerify,
            CURLOPT_POSTFIELDS => http_build_query($body),
            CURLOPT_POST => $post,
        ];
        
        $options = curl_setopt_array($this->cUrl, $array);

        if ($options) {
            return $options;
        }
        
        throw new Exception("Erro ao realizar requisição, verifique! Codigo do erro: 101", 500);
    }

    public function execute(?CurlHandle $curlHandle): string
    {
        $curlHandle ??= $this->cUrl;
        
        $response = curl_exec($curlHandle);

        if ($response === false) {
            throw new Exception("Erro ao realizar requisição, verifique! Codigo do erro: 100", 500);
        }

        return $response;
    }

    public function close(?CurlHandle $curlHandle): void
    {
        $curlHandle ??= $this->cUrl;

        curl_close($curlHandle);

        return;
    }

    public function makeGet(string $url): string
    {
        $this->init();
        $this->options($url);
        $response = $this->execute($this->cUrl);

        $this->close($this->cUrl);
        
        return $response;
    }
}
