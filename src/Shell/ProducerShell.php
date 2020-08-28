<?php

namespace App\Shell;

use Cake\Console\Shell;

class ProducerShell extends Shell
{
  public function main() {
    $config = \Kafka\ProducerConfig::getInstance();
    $config->setMetadataRefreshIntervalMs(10000);
    $config->setMetadataBrokerList('kafka:9092');
    $config->setBrokerVersion('1.0.0');
    $config->setRequiredAck(1);
    $config->setIsAsyn(false);
    $config->setProduceInterval(500);

    $producer = new \Kafka\Producer( function() {
      return [[ 'topic' => 'test_kafka_for_training', 'value' => 'test....message.', 'key' => 'testkey',]];
    });
    $producer->success(function($result) {
      $this->out(serialize($result));
    });
    $producer->error(function($errorCode) {
      $this->out($errorCode);
    });
    $producer->send(true);
    
  }
}