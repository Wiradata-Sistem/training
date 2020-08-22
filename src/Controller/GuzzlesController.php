<?php
declare(strict_types=1);
namespace App\Controller;

use GuzzleHttp\Client;

class GuzzlesController extends AppController 
{
    public function index()
    {
        $client = new Client();
        $response = $client->get('https://nginx/groups', ['verify' => false]);
        $res = get_object_vars(json_decode($response->getBody()->__toString()));
        
        $response = [
            'status_code' => 'cdc-299',
            'status_message' => 'invalid json format response',
            'data' =>$res
        ];
        
        if (is_array($res) && isset($res['status_code']) && isset($res['status_message']) && isset($res['data'])) {
            $response = [
                'status_code' => $res['status_code'],
                'status_message' => $res['status_message'],
                'data' => $res['data']
            ];
        }

        $this->set(['status_code', 'status_message', 'data'], $response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

    public function view($id)
    {
        $client = new Client();
        $response = $client->get('https://nginx/groups/'.$id, ['verify' => false]);
        $res = get_object_vars(json_decode($response->getBody()->__toString()));
        
        $response = [
            'status_code' => 'cdc-299',
            'status_message' => 'invalid json format response',
            'data' =>$res
        ];
        
        if (is_array($res) && isset($res['status_code']) && isset($res['status_message']) && isset($res['data'])) {
            $response = [
                'status_code' => $res['status_code'],
                'status_message' => $res['status_message'],
                'data' => $res['data']
            ];
        }

        $this->set(['status_code', 'status_message', 'data'], $response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);

    }

    public function add()
    {
        $this->request->allowMethod(['post', 'put']);
        $payload = [
            'name' => $this->request->getData('name')
        ];

        $client = new Client();
        $response = $client->post('https://nginx/groups', ['verify' => false, 'json' => $payload]);
        $res = get_object_vars(json_decode($response->getBody()->__toString()));
        $this->response = $this->response->withStatus($response->getStatusCode());
        
        $response = [
            'status_code' => 'cdc-299',
            'status_message' => 'invalid json format response',
            'data' =>$res
        ];
        
        if (is_array($res) && isset($res['status_code']) && isset($res['status_message']) && isset($res['data'])) {
            $response = [
                'status_code' => $res['status_code'],
                'status_message' => $res['status_message'],
                'data' => $res['data']
            ];
        }

        $this->set(['status_code', 'status_message', 'data'], $response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);

    }

    public function edit($id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $payload = [
            'name' => $this->request->getData('name')
        ];

        $client = new Client();
        $response = $client->put('https://nginx/groups/'.$id, ['verify' => false, 'json' => $payload]);
        $res = get_object_vars(json_decode($response->getBody()->__toString()));
        $this->response = $this->response->withStatus($response->getStatusCode());
        
        $response = [
            'status_code' => 'cdc-299',
            'status_message' => 'invalid json format response',
            'data' =>$res
        ];
        
        if (is_array($res) && isset($res['status_code']) && isset($res['status_message']) && isset($res['data'])) {
            $response = [
                'status_code' => $res['status_code'],
                'status_message' => $res['status_message'],
                'data' => $res['data']
            ];
        }

        $this->set(['status_code', 'status_message', 'data'], $response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);

    }

    public function delete($id)
    {
        $this->request->allowMethod(['delete']);

        $client = new Client();
        $response = $client->delete('https://nginx/groups/'.$id, ['verify' => false]);
        return $this->response->withStatus($response->getStatusCode());
    }

}