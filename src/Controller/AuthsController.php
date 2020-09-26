<?php
declare(strict_types=1);
namespace App\Controller;

use Cake\Validation\Validator;
use Cake\Http\Exception\BadRequestException;

class AuthsController extends AppController 
{
    public function login()
    {
        $this->request->allowMethod(['post']);
        // your login code

        // Validation
        $validator = new Validator();
        $validator->requirePresence('username')->requirePresence('password')->notEmpty(['username', 'password']);
        $errors = $validator->validate($this->request->getData());
        if (!empty($errors)) {
            foreach ($errors as $key => $err) {
                foreach($err as $k => $e) {
                    throw new BadRequestException($key . ' is ' . ltrim($k, '_'));
                    break 2;
                }
            }
        }

        // Query to User DB
        $this->loadModel('Users');
        $user = $this->Users->find()->where(['username' => $this->request->getData('username')])->first();
        if (empty($user)) {
            throw new BadRequestException(__('Invalid username/password'));
        }
        $passwordWithSalt = env('SECURITY_SALT', '20a154bd44cf73a3ef2dc4caf4d8922e561deb6d12c4a946379b05d0cfe0deea') . $this->request->getData('password');
        if (!password_verify($passwordWithSalt, $user['password'])) {
            throw new BadRequestException(__('Invalid username/password'));
        }

        // Get Token
        $payload = json_encode([
            'exp' => time() + 3600,
            'username' => $user['username']
        ]);
        $this->loadComponent('Jwt');
        $token = $this->Jwt->GetToken($payload);

        $response = [
            'status_code' => 'cdc-200',
            'status_message'=> 'success',
            'data' => [ 'token' => $token]
        ];
        $this->set($response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

}