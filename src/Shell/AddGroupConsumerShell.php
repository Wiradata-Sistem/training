<?php

namespace App\Shell;

use Cake\Console\Shell;

class AddGroupConsumerShell extends Shell
{
  public function main() 
  {
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
  }
}