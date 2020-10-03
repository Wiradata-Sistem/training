<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\JwtComponent;
use Cake\Controller\Controller;
use App\Controller\Component\AclComponent;

class AuthApiMiddleware extends ApiMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        if (!in_array($request->getParam('_matchedRoute'), ['/login', '/forgot-password', '/change-password/:token'])) {
			if (!$request->hasHeader('Authorization')) {
				return $this->notAuthException(new \Exception('Please Login'), $request);
			}
			$token = explode('Bearer ', $request->getHeader('Authorization')[0]);
			if (!isset($token) || !isset($token[1])) {
				return $this->notAuthException(new \Exception('Please Suppy Valid Token'), $request);
			}
			$registry = new ComponentRegistry(new Controller($request, null, 'Auths'));
			$jwt = new JwtComponent($registry);
			list($isValid, $data) = $jwt->Claim($token[1]);
			if (!$isValid) {
				return $this->notAuthException(new \Exception($data), $request);
			}
			$request->withAttribute('username', $data['username']);
			$acl = new AclComponent($registry);
			if (!$acl->Check($data['username'], $request->getParam('controller'), $request->getParam('action'))) {
				return $this->notAuthException(new \Exception('Not Authorization'), $request);
			}
		}
		return $handler->handle($request);
    }

}
