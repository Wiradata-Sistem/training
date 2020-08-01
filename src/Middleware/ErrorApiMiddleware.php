<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Cake\Http\Response;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Core\App;
use Cake\Core\InstanceConfigTrait;
use Cake\Error\ErrorHandler;
use Cake\Http\Exception\NotFoundException;


class ErrorApiMiddleware implements MiddlewareInterface
{
    use InstanceConfigTrait;
    
    protected $_defaultConfig = [
        'skipLog' => [],
        'log' => true,
        'trace' => false,
        'exceptionRenderer' => ExceptionRenderer::class,
    ];

    protected $errorHandler; 

    public function __construct($errorHandler = [])
    {
        if (is_array($errorHandler)) {
            $this->setConfig($errorHandler);
            return;
        }
        $this->errorHandler = $errorHandler;    
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        try {
            return $handler->handle($request);
        } catch (RecordNotFoundException $exception) {
            return $this->recordNotFoundException($exception, $request);
        } catch (NotFoundException $exception) {
            return $this->notFoundException($exception, $request);
        } catch (Throwable $exception) {
            return $this->handleException($exception, $request);
        }

    }

    public function handleException(Throwable $exception, ServerRequestInterface $request): ResponseInterface    
    {
        $errorHandler = $this->getErrorHandler();
        $response = (new Response)->withType('application/json');
        $json =  ["status_code" => "cdc-002", "status_message" => "ops! internal server error", "data"=> null];
        try {
            $json["status_code"] = "cdc-003";
            $json["status_message"] = $exception->getMessage();
            $response = $response->withStatus(500);
            $errorHandler->logException($exception, $request);
        } catch (Throwable $internalException) {
            $response = $response->withStatus(500);
            $errorHandler->logException($internalException, $request);
        }
        return $response->withStringBody(json_encode($json));
    }

    public function notFoundException(Throwable $exception, ServerRequestInterface $request): ResponseInterface    
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

    public function recordNotFoundException(Throwable $exception, ServerRequestInterface $request): ResponseInterface    
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

    protected function getErrorHandler(): ErrorHandler
    {
        if ($this->errorHandler === null) {
            $className = App::className('ErrorHandler', 'Error');
            $this->errorHandler = new $className($this->getConfig());
        }
        return $this->errorHandler;
    }

}
