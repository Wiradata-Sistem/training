<?php
declare(strict_types=1);
namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;

class GroupsController extends AppController 
{
    public function index()
    {
        $page = $this->request->getQuery('page', 1);
        $limit = $this->request->getQuery('limit', 10);
        $order = $this->request->getQuery('order', 'id');
        $sort = $this->request->getQuery('sort', 'asc');
        $keyword = $this->request->getQuery('keyword');

        $page = intval($page);
        $limit = intval($limit);

        if (!in_array($order, ['id', 'name'])) {
            $order = "id";
        }
        if ($page < 1) {
            $page = 1;
        }
        if ($limit < 1) {
            $limit = 1;
        }
        $sort = strtolower($sort);
        if (!in_array($sort, ['asc', 'desc'])) {
            $sort = 'asc';
        }
        
        $groups = $this->Groups->find()->order([$order => $sort]);
        if ($keyword) {
            $groups = $groups->where(['name LIKE ' => "%$keyword%"]);
        }

        $count = $groups->count();
        if ($limit > $count) {
            $limit = $count;
        }
        if (($page * $limit) > $count) {
            $page = intval(ceil($count/$limit));
        }

        $groups = $groups->limit($limit)->page($page)->all();
        
        if (empty($groups->toArray())) {
            throw new RecordNotFoundException(__('Group not found'));
        }
        
        $pagination = [
            'page' => $page,
            'limit' => $limit,
            'order' => $order,
            'sort' => $sort,
            'count' => $count,
            'keyword' => $keyword
        ];        

        $this->set(['pagination', 'groups'], [$pagination, $groups]);
        $this->viewBuilder()->setOption('serialize', ['groups', 'pagination']);
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