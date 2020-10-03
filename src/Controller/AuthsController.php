<?php
declare(strict_types=1);
namespace App\Controller;

use Cake\Validation\Validator;
use Cake\Http\Exception\BadRequestException;
use JackyHtg\Tools;

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

    public function forgot()
    {
        $this->request->allowMethod(['post']);
        $this->loadModel('ForgotPasswords');
        $this->loadModel('Users');
        
        // email validation
        if (!filter_var($this->request->getData('email'), FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestException(__('Please supply valid email'));
        }

        // check to db
        $user = $this->Users->find()->where(['email' => $this->request->getData('email')])->first();
        if (empty($user)) {
            throw new BadRequestException(__('Invalid email'));
        }

        // create link and store to forgot password db
        $forgot = $this->ForgotPasswords->newEntity([
            'token' => sha1($user['id'] . time() . env('SECURITY_SALT', '20a154bd44cf73a3ef2dc4caf4d8922e561deb6d12c4a946379b05d0cfe0deea')),
            'user_id' => $user['id'],
            'is_used' => false,
            'expired' => date('Y-m-d H:i:s', (time() + (2 * 24 * 60 * 60)))
        ]);
        $this->ForgotPasswords->getConnection()->begin();
        if (!$this->ForgotPasswords->saveOrFail($forgot, ['atomic' => false])) {
            $this->ForgotPasswords->getConnection()->rollback();
            throw new \Error(__('Error save Forgot Password'));
        }
        $this->ForgotPasswords->getConnection()->commit();

        // send email of forgot password
        $from = [
            'name' => env('SENDGRID_FROM_NAME', null),
            'email' => env('SENDGRID_FROM_EMAIL', null)
        ];
        $toEmails = [[
            'name' => $user['name'],
            'email' => $user['email']
        ]];
        $data = [
            'name' => $user['name'],
            'url' => "https://erp.local/change-password/".$forgot['token'],
            'app_name' => env('APP_NAME', null),
            'cs_email' => 'cs@rijalasepnugroho.com',
            'cs_phone' => '021-22222222'
        ];
        $this->loadComponent('Email');
        if (!$this->Email->Send($from, $toEmails, $data, env('SENDGRID_TEMPLATE_FORGOT_PASSWORD', null))) {
            $this->ForgotPasswords->getConnection()->rollback();
            throw new \Exception(__('Error send email'));
        }

        $this->set([
            'status_code' => 'cdc-200',
            'status_message' => 'Link penggantian password telah dikirim lewat email',
            'data' => null
        ]);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

    public function changePassword(string $token) {
        $this->request->allowMethod(['post']);
        $this->loadModel('ForgotPasswords');
        $this->loadModel('Users');

        // validation
        $validator = new Validator();
        $validator->requirePresence('new_password')->requirePresence('re_password')->notEmpty(['new_password', 're_password']);
        $errors = $validator->validate($this->request->getData());
        if (!empty($errors)) {
            foreach ($errors as $key => $err) {
                foreach($err as $k => $e) {
                    throw new BadRequestException($key . ' is ' . ltrim($k, '_'));
                    break 2;
                }
            }
        }

        // match password validation
        if ($this->request->getData('new_password') <> $this->request->getData('re_password')) {
            throw new BadRequestException(__('Password not match'));
        }

        // strong password checking
        $tools = new Tools("/usr/sbin/cracklib-check", "/usr/bin/pwscore");
        if ($tools->PasswordMeter($this->request->getData('new_password')) < 70) {
            throw new BadRequestException(__('Please supply strong Password'));
        }
        
        // Token Validation 
        $forgot = $this->ForgotPasswords->find()->where(['token' => $token, 'is_used' => 0])->first();
        if (empty($forgot)) {
            throw new BadRequestException(__('Invalid Token'));
        }

        $expired = (array) $forgot['expired'];
        if (strtotime($expired['date']) < time()) {
            throw new \Exception(__('link was expired'));
        }

        // Change password of user
        $user = $this->Users->get($forgot['user_id']);
        $user = $this->Users->patchEntity($user, ['password' => $this->request->getData('new_password')]);
        $this->Users->getConnection()->begin();
        if (!$this->Users->saveOrFail($user, ['atomic' => false])) {
            $this->Users->getConnection()->rollback();
            throw new \Exception(__('Error update password'));
        }

        // update link forgor password, set is_used = true
        $forgot['is_used'] = 1;
        if (!$this->ForgotPasswords->saveOrFail($forgot, ['atomic' => true])) {
            $this->Users->getConnection()->rollback();
            throw new \Exception(__('Error update forgot password link'));
        }

        $this->set([
            'status_code' => 'cdc-200',
            'status_message' => 'Password berhasil diganti',
            'data' => null
        ]);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

}