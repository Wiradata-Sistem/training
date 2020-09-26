<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Cake\Http\Response;

class ApiMiddleware
{
    protected function notFoundException(Throwable $exception, ServerRequestInterface $request): ResponseInterface    
    {
        $response = (new Response)->withType('application/json');
        $json =  ["status_code" => "cdc-002", "status_message" => "ops! internal server error", "data"=> null];
        try {
            $json["status_code"] = "cdc-001";
            $json["status_message"] = $exception->getMessage();
            $response = $response->withStatus(404);
        } catch (Throwable $internalException) {
                $response = $response->withStatus(500);
            }
        return $response->withStringBody(json_encode($json));
    }

    protected function recordNotFoundException(Throwable $exception, ServerRequestInterface $request): ResponseInterface    
    {
        $response = (new Response)->withType('application/json');
        $json =  ["status_code" => "cdc-002", "status_message" => "ops! internal server error", "data"=> null];
        try {
            $json["status_code"] = "cdc-0011";
            $json["status_message"] = $exception->getMessage();
            $response = $response->withStatus(404);
        } catch (Throwable $internalException) {
                $response = $response->withStatus(500);
            }
        return $response->withStringBody(json_encode($json));
    }

    protected function notAuthException(Throwable $exception, ServerRequestInterface $request): ResponseInterface   
    {
        $response = (new Response)->withType('application/json');
        $json =  ["status_code" => "cdc-002", "status_message" => "ops! internal server error", "data"=> null];
        try {
            $json["status_code"] = "cdc-0011";
            $json["status_message"] = $exception->getMessage();
            $response = $response->withStatus(401);
        } catch (Throwable $internalException) {
            $response = $response->withStatus(500);
        }
        return $response->withStringBody(json_encode($json));
    }


}
