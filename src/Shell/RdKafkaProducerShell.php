<?php

namespace App\Shell;

use Cake\Console\Shell;

class RdKafkaProducerShell extends Shell
{
  public function main() {
    $conf = new \RdKafka\Conf();
    $rk = new \RdKafka\Producer($conf);
    $rk->addBrokers("kafka:9092");

    $topic = $rk->newTopic("testRDKAFKA");
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, "Message payload .......");
    $rk->flush(500);
    
  }
}