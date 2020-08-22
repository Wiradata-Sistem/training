<?php
namespace App\Shell;

use Cake\Console\Shell;

class ConsumerShell extends Shell
{
    public function main()
    {
        $config = \Kafka\ConsumerConfig::getInstance();
        $config->setMetadataRefreshIntervalMs(10000);
        $config->setMetadataBrokerList('kafka:9092');
        $config->setGroupId('cdc');
        $config->setBrokerVersion('1.0.0');
        $config->setTopics(['test_kafka_for_training']);

        $consumer = new \Kafka\Consumer();
        $consumer->start(function($topic, $part, $message) {
            $this->out(serialize($message));
        });

    }
}
