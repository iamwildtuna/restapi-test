<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\TestRabbit;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class TestRabbitHandler implements MessageHandlerInterface
{
    public function __construct()
    {
        // TODO тут можно что-то заавтовайрить
    }

    public function __invoke(TestRabbit $message)
    {
        echo 'AUTO:';
    }
}