<?php

namespace App\Shell;

use Cake\Console\Shell;

class RdKafkaConsumerShell extends Shell
{
  public function main() {
    $conf = new \RdKafka\Conf();
    $rk = new \RdKafka\Consumer($conf);
    $rk->addBrokers("kafka");
    $topic = $rk->newTopic("testRDKAFKA");
    $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

    while (true) {
      $msg = $topic->consume(0, 1000);
      if (null === $msg || $msg->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
        continue;
      } elseif ($msg->err) {
        echo $msg->errstr(), "\n";
        break;
      } else {
        echo $msg->payload, "\n";
      }
    } 
  }
}