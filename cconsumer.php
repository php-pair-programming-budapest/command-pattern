<?php

require __DIR__.'/bootstrap.php';


use Bernard\Consumer;
use Bernard\Router\SimpleRouter;
use League\Tactician\Bernard\Receiver\SameBusReceiver;
use Symfony\Component\EventDispatcher\EventDispatcher;

// Wire the command bus into Bernard's routing system
$receiver = new SameBusReceiver($commandBus);
$router = new SimpleRouter();
$router->add('League\Tactician\Bernard\QueueableCommand', $receiver);

$queue = $queueFactory->create('register-user');

// Finally, create the Bernard consumer that runs through the pending queue
$consumer = new Consumer($router, new EventDispatcher());
$consumer->consume($queue);
