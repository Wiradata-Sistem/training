<?php
declare(strict_types=1);
namespace App\Controller;

use Cake\Http\Client;

class HttpClientsController extends AppController 
{
    public function index()
    {
        $client = new Client();
        $response = $client->get('https://nginx/groups', [], ['ssl_verify_peer' => false, 'ssl_verify_host' => false]);
        $res = $response->getJson();
        
        $response = [
            'status_code' => 'cdc-299',
            'status_message' => 'invalid json format response',
            'data' =>$res
        ];
        
        if (is_array($res) && isset($res['status_code']) && isset($res['status_message']) && (isset($res['data']) || $res['data'] === null)) {
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
        $response = $client->get('https://nginx/groups/'.$id, [], ['ssl_verify_peer' => false, 'ssl_verify_host' => false]);
        $res = $response->getJson();
        
        $response = [
            'status_code' => 'cdc-299',
            'status_message' => 'invalid json format response',
            'data' =>$res
        ];
        
        if (is_array($res) && isset($res['status_code']) && isset($res['status_message']) && (isset($res['data']) || $res['data'] === null)) {
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
        $response = $client->post(
            'https://nginx/groups', 
            json_encode($payload), 
            ['ssl_verify_peer' => false, 'ssl_verify_host' => false, 'type' => 'json']
        );
        $res = $response->getJson();
        $this->response = $this->response->withStatus($response->getStatusCode());
        
        $response = [
            'status_code' => 'cdc-299',
            'status_message' => 'invalid json format response',
            'data' =>$res
        ];
        
        if (is_array($res) && isset($res['status_code']) && isset($res['status_message']) && (isset($res['data']) || $res['data'] === null)) {
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
        $httpResponse = $client->put(
            'https://nginx/groups/'.$id, 
            json_encode($payload), 
            ['ssl_verify_peer' => false, 'ssl_verify_host' => false, 'type' => 'json']
        );
        $res = $httpResponse->getJson();
        
        $this->response = $this->response->withStatus($httpResponse->getStatusCode());
        
        $response = [
            'status_code' => 'cdc-299',
            'status_message' => 'invalid json format response',
            'data' =>$res
        ];
        
        if (is_array($res) && isset($res['status_code']) && isset($res['status_message']) && (isset($res['data']) || $res['data'] === null)) {
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
        $httpResponse = $client->delete('https://nginx/groups/'.$id, [], ['ssl_verify_peer' => false, 'ssl_verify_host' => false]);
        $res = $httpResponse->getJson();
        
        $this->response = $this->response->withStatus($httpResponse->getStatusCode());

        if ($httpResponse->getStatusCode() === 204) {
            return $this->response;
        }
        
        $response = [
            'status_code' => 'cdc-299',
            'status_message' => 'invalid json format response',
            'data' =>$res
        ];
        
        if (is_array($res) && isset($res['status_code']) && isset($res['status_message']) && (isset($res['data']) || $res['data'] === null)) {
            $response = [
                'status_code' => $res['status_code'],
                'status_message' => $res['status_message'],
                'data' => $res['data']
            ];
        }

        $this->set(['status_code', 'status_message', 'data'], $response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

}