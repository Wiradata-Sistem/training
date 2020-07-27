<?php
declare(strict_types=1);
namespace App\Controller;

class GroupsController extends AppController 
{
    public function index()
    {
        $groups = $this->Groups->find('all');
        $this->set('groups', $groups);
        $this->viewBuilder()->setOption('serialize', ['groups']);
    }

    public function view($id)
    {
        $group = $this->Groups->get($id);
        $this->set('group', $group);
        $this->viewBuilder()->setOption('serialize', ['group']);
    }

    public function add()
    {
        $this->request->allowMethod(['post', 'put']);
        $group = $this->Groups->newEntity($this->request->getData());
        if ($this->Groups->save($group)) {
            $message = 'Saved';
        } else {
            $message = 'Error';
        }
        $this->set([
            'message' => $message,
            'group' => $group,
        ]);
        $this->viewBuilder()->setOption('serialize', ['group', 'message']);
    }

    public function edit($id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $group = $this->Groups->get($id);
        $group = $this->Groups->patchEntity($group, $this->request->getData());
        if ($this->Groups->save($group)) {
                $message = 'Saved';
        } else {
                $message = 'Error';
        }
        $this->set([
                'message' => $message,
                'group' => $group,
        ]);
        $this->viewBuilder()->setOption('serialize', ['group', 'message']);
    }

    public function delete($id)
    {
        $this->request->allowMethod(['delete']);
        $group = $this->Groups->get($id);
        $message = 'Deleted';
        if (!$this->Groups->delete($group)) {
                $message = 'Error';
        }
        $this->set('message', $message);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}