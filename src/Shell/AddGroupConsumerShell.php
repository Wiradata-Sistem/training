<?php

namespace App\Shell;

use Cake\Console\Shell;

class AddGroupConsumerShell extends Shell
{
  public function main() 
  {
    /*
    $config = \Kafka\ConsumerConfig::getInstance();
    $config->setMetadataRefreshIntervalMs(10000);
    $config->setMetadataBrokerList('kafka:9092');
    $config->setGroupId('cdc');
    $config->setBrokerVersion('1.0.0');
    $config->setTopics(['add_group']);

    $consumer = new \Kafka\Consumer();
    $consumer->start(function($topic, $part, $message) {
        $Groups = $this->getTableLocator()->get('Groups');
        $group = $Groups->newEntity(unserialize($message['message']['value']));
        if ($Groups->save($group)) {
            $this->out("success add new group");
        } else {
            $this->out("error add new group");
        }
    });
    */
    $conf = new \RdKafka\Conf();
    $conf->set('group.id', 'addGroup');
    $rk = new \RdKafka\Consumer($conf);
    $rk->addBrokers("kafka");
    $topic = $rk->newTopic("add_group");
    $topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);

    $producer = new \RdKafka\Producer(new \RdKafka\Conf());
    $producer->addBrokers("kafka:9092");
    $producerTopic = $producer->newTopic("res_add_group");
        

    while (true) {
      $response = [
        "status_code" => null,
        "status_message" => null,
        "data" => null
      ];
      $key = null;
      
      $msg = $topic->consume(0, 1000);
      if (null === $msg || $msg->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
        continue;
      } elseif ($msg->err) {
        $this->out($msg->errstr());
        $response['status_code'] = 'cdc-100';
        $response['status_message'] = $msg->errstr();
        break;
      } else {
        $Groups = $this->getTableLocator()->get('Groups');
        $data = unserialize($msg->payload);
        $key = $data["key"];
        $groupEntity = $data["data"];
        $group = $Groups->newEntity($groupEntity);
        if ($Groups->save($group)) {
          $this->out("success add new group key ". $key);
          $response['status_code'] = 'cdc-200';
          $response['status_message'] = "success add new group";
          $response['data'] = $group;
        } else {
          $this->out("error add new group");
          $response['status_code'] = 'cdc-101';
          $response['status_message'] = 'error add new group';
        }
      }

      $producerTopic->produce(RD_KAFKA_PARTITION_UA, 0, serialize(["key"=>$data["key"], "response" => $response]));
      $producer->flush(500);
    }
  }
}