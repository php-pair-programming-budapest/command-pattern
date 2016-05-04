<?php

require __DIR__.'/bootstrap.php';


$command = new RegisterUser();
$command->emailAddress = 'mark.sagikazar@gmail.com';
$command->password = 'nagyontitok';

$commandBus->handle($command);
