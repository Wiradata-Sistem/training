<?php
declare(strict_types=1);
namespace App\Controller;

class UsersController extends AppController 
{
    public function index()
    {
        $Users = $this->Users->find('all');
        $this->set('Users', $Users);
        $this->viewBuilder()->setOption('serialize', ['Users']);
    }

    public function view($id)
    {
        $User = $this->Users->get($id);
        $this->set('User', $User);
        $this->viewBuilder()->setOption('serialize', ['User']);
    }

    public function add()
    {
        $this->request->allowMethod(['post', 'put']);
        $User = $this->Users->newEntity($this->request->getData());
        if ($this->Users->save($User)) {
            $message = 'Saved';
        } else {
            $message = 'Error';
        }
        $this->set([
            'message' => $message,
            'User' => $User,
        ]);
        $this->viewBuilder()->setOption('serialize', ['User', 'message']);
    }

    public function edit($id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $User = $this->Users->get($id);
        $User = $this->Users->patchEntity($User, $this->request->getData());
        if ($this->Users->save($User)) {
                $message = 'Saved';
        } else {
                $message = 'Error';
        }
        $this->set([
                'message' => $message,
                'User' => $User,
        ]);
        $this->viewBuilder()->setOption('serialize', ['User', 'message']);
    }

    public function delete($id)
    {
        $this->request->allowMethod(['delete']);
        $User = $this->Users->get($id);
        $message = 'Deleted';
        if (!$this->Users->delete($User)) {
                $message = 'Error';
        }
        $this->set('message', $message);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}