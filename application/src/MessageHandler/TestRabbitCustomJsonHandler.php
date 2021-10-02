<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\TestRabbitCustomJson;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class TestRabbitCustomJsonHandler implements MessageHandlerInterface
{
    public function __construct()
    {
        // TODO тут можно что-то заавтовайрить
    }

    public function __invoke(TestRabbitCustomJson $message)
    {
        echo 'CUSTOM:';
    }
}