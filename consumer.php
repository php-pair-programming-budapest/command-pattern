<?php

require __DIR__.'/vendor/autoload.php';

use Bernard\Driver\PheanstalkDriver;
use Pheanstalk\Pheanstalk;

$pheanstalk = new Pheanstalk('localhost');

$driver = new PheanstalkDriver($pheanstalk);

$queueFactory = new \Bernard\QueueFactory\PersistentFactory($driver, new \Bernard\Serializer());

$queue = $queueFactory->create('test');

$envelope = $queue->dequeue();

$message = $envelope->getMessage();

$queue->acknowledge($envelope);

var_dump($message);
