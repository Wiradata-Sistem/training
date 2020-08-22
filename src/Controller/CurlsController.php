<?php
declare(strict_types=1);
namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Cache\Cache;

class CurlsController extends AppController 
{
    public function index()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://nginx/groups');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $res = get_object_vars(json_decode(curl_exec($ch)));

        if (curl_error($ch)) {
            $response = [
                'status_code' => 'cdc-100',
                'status_message' => curl_error($ch),
                'data' => null
            ];
        } else {
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
        }

        curl_close($ch);


        $this->set(['status_code', 'status_message', 'data'], $response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

    public function view($id)
    {
        $cacheKey = 'groups::'.$id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://nginx/groups/'.$id);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $res = get_object_vars(json_decode(curl_exec($ch)));

        if (curl_error($ch)) {
            $response = [
                'status_code' => 'cdc-100',
                'status_message' => curl_error($ch),
                'data' => null
            ];
        } else {
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
        }

        curl_close($ch);

        $this->set(['status_code', 'status_message', 'data'], $response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

    public function add()
    {
        $this->request->allowMethod(['post', 'put']);
        $payload = [
            'name' => $this->request->getData('name')
        ];

        $header = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://nginx/groups');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $res = get_object_vars(json_decode(curl_exec($ch)));

        if (curl_error($ch)) {
            $response = [
                'status_code' => 'cdc-100',
                'status_message' => curl_error($ch),
                'data' => null
            ];
        } else {
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
        }

        curl_close($ch);

        $this->set(['status_code', 'status_message', 'data'], $response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

    public function edit($id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $payload = [
            'name' => $this->request->getData('name')
        ];

        $header = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://nginx/groups/'.$id);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        $res = get_object_vars(json_decode(curl_exec($ch)));

        if (curl_error($ch)) {
            $response = [
                'status_code' => 'cdc-100',
                'status_message' => curl_error($ch),
                'data' => null
            ];
        } else {
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
        }

        curl_close($ch);

        $this->set(['status_code', 'status_message', 'data'], $response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

    public function delete($id)
    {
        $this->request->allowMethod(['delete']);

        /*$header = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];*/

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://nginx/groups/'.$id);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        $body = json_decode(curl_exec($ch));
    
        if ($body) {
            $res = get_object_vars($body);    
        }
            
        $response = [
            'status_code' => 'cdc-200',
            'status_message' => 'success',
            'data' => null
        ];

        if (curl_error($ch)) {
            throw new Exception(curl_error($ch));
        } else {
            if (isset($res) && is_array($res) && isset($res['status_code']) && isset($res['status_message']) && (isset($res['data']) || $res['data'] === null )) {
                $response = [
                    'status_code' => $res['status_code'],
                    'status_message' => $res['status_message'],
                    'data' => $res['data']
                ];
            }

            $this->response = $this->response->withStatus(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        }

        curl_close($ch);

        $this->set(['status_code', 'status_message', 'data'], $response);
        $this->viewBuilder()->setOption('serialize', ['status_code', 'status_message', 'data']);
    }

}