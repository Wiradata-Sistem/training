<?php
declare(strict_types=1);
namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Cache\Cache;

class GroupsController extends AppController 
{
    public function index()
    {
        $page = $this->request->getQuery('page', 1);
        $limit = $this->request->getQuery('limit', 10);
        $order = $this->request->getQuery('order', 'id');
        $sort = $this->request->getQuery('sort', 'asc');
        $keyword = $this->request->getQuery('keyword');

        $cacheKey = "groups::list::" . serialize([
            "page" => $page,
            "limit" => $limit,
            "sort" => $sort,
            "keyword" => $keyword
        ]);

        $response = $this->redis->get($cacheKey);
        if (!$response) {
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

            $response = [
                "status_code" => "cdc-200",
                "status_message" => "success",
                "data" => [
                    "groups" => $groups,
                    "pagination" => $pagination
                ]
            ];

	        $this->redis->set($cacheKey, serialize($response));
        } else {
            $response = unserialize($response);
        } 

        $this->set(['status_code', 'status_message', 'data'], $response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

    public function view($id)
    {
        $cacheKey = 'groups::'.$id;
        $response = $this->redis->get($cacheKey);
        if (!$response) {
            $group = $this->Groups->get($id);
            $response = [
                "status_code" => "cdc-200",
                "status_message" => "success",
                "data" => $group
            ];
            $this->redis->set($cacheKey, serialize($response));
        } else {
            $response = unserialize($response);
        }
        $this->set(['status_code', 'status_message', 'data'], $response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

    public function add()
    {
        $this->request->allowMethod(['post', 'put']);
        $group = $this->Groups->newEntity($this->request->getData());
        if ($this->Groups->save($group)) {
            $message = 'Saved';
            $statusCode = 'cdc-200';

            $groupCaches = $this->redis->keys('groups::list*');
            foreach ($groupCaches as $c) {
                $this->redis->del($c);
            }
        } else {
            $message = 'Error';
            $statusCode = 'cdc-115';
        }

        $this->set([
            'status_code' => $statusCode,
            'status_message' => $message,
            'data' => $group,
        ]);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

    public function edit($id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $group = $this->Groups->get($id);
        $group = $this->Groups->patchEntity($group, $this->request->getData());
        if ($this->Groups->save($group)) {
                $message = 'Saved';
                $statusCode = 'cdc-200';
                $groupCaches = $this->redis->keys('groups::list*');
                foreach ($groupCaches as $c) {
                    $this->redis->del($c);
                }

                $this->redis->del('groups::'.$id);

        } else {
                $message = 'Error';
                $statusCode = 'cdc-115';
        }
        $this->set([
                'status_message' => $message,
                'data' => $group,
                'status_code' => $statusCode
        ]);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

    public function delete($id)
    {
        $this->request->allowMethod(['delete']);
        $group = $this->Groups->get($id);
        $message = 'Deleted';
        $statusCode = 'cdc-200';
        if (!$this->Groups->delete($group)) {
                $message = 'Error';
                $statusCode = 'cdc-115';

                $groupCaches = $this->redis->keys('groups::list*');
                foreach ($groupCaches as $c) {
                    $this->redis->del($c);
                }

                $this->redis->del('groups::'.$id);
        }
        $this->set([
            'status_message' => $message,
            'data' => null,
            'status_code' => $statusCode
        ]);
        $this->response = $this->response->withStatus(204);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

}