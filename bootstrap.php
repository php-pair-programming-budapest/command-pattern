<?php

require __DIR__.'/vendor/autoload.php';

use Bernard\Producer;
use League\Tactician\Bernard\QueueMiddleware;
use League\Tactician\CommandBus;
use Bernard\Driver\PheanstalkDriver;
use Normalt\Normalizer\AggregateNormalizer;
use Pheanstalk\Pheanstalk;use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;


class RegisterUser implements \League\Tactician\Bernard\QueueableCommand
{
    public $emailAddress;
    public $password;

    public function getName()
    {
        return 'RegisterUser';
    }
}

class RegisterUserHandler
{
    public function handleRegisterUser(RegisterUser $command)
    {
        // Do your core application logic here. Don't actually echo things. :)
        echo "User {$command->emailAddress} was registered!\n";
    }
}

class RegisterUserNormalizer implements \Symfony\Component\Serializer\Normalizer\NormalizerInterface, \Symfony\Component\Serializer\Normalizer\DenormalizerInterface
{
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $command = new RegisterUser();
        $command->emailAddress = $data['emailAddress'];
        $command->password = $data['password'];

        return $command;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === RegisterUser::class;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'emailAddress' => $object->emailAddress,
            'password' => $object->password,
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof RegisterUser;
    }
}

$pheanstalk = new Pheanstalk('localhost');

$driver = new PheanstalkDriver($pheanstalk);

$normalizer = new AggregateNormalizer([
    new RegisterUserNormalizer(),
    new \Bernard\Normalizer\EnvelopeNormalizer(),
    new \Bernard\Normalizer\DefaultMessageNormalizer(),
]);

$queueFactory = new \Bernard\QueueFactory\PersistentFactory($driver, new \Bernard\Serializer($normalizer));

$producer = new Producer($queueFactory, new \Symfony\Component\EventDispatcher\EventDispatcher());

$queueMiddleware = new QueueMiddleware($producer);

$locator = new InMemoryLocator();
$locator->addHandler(new RegisterUserHandler(), RegisterUser::class);
$handlerMiddleware = new League\Tactician\Handler\CommandHandlerMiddleware(
    new ClassNameExtractor(),
    $locator,
    new HandleClassNameInflector()
);

$commandBus = new CommandBus([
    $queueMiddleware,
    $handlerMiddleware,
]);
