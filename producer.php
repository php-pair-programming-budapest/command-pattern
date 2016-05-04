<?php

require __DIR__.'/vendor/autoload.php';

use Bernard\Driver\PheanstalkDriver;
use Pheanstalk\Pheanstalk;

$pheanstalk = new Pheanstalk('localhost');

$driver = new PheanstalkDriver($pheanstalk);

$queueFactory = new \Bernard\QueueFactory\PersistentFactory($driver, new \Bernard\Serializer());

$queue = $queueFactory->create('test');

$message = new \Bernard\Message\DefaultMessage('sendEmail', ['message' => 'Teszt', 'to' => 'mark.sagikazar@gmail.com']);
$envelope = new \Bernard\Envelope($message);

$queue->enqueue($envelope);
