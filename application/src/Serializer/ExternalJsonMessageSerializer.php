<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Message\TestRabbitCustomJson;
use JsonException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class ExternalJsonMessageSerializer implements SerializerInterface
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $gelfLogger)
    {
        $this->logger = $gelfLogger;
    }

    /**
     * @param  array  $encodedEnvelope
     *
     * @return Envelope
     * @throws JsonException
     * @throws RuntimeException
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];
        $this->logger->info('AMQP message: '.$body);
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        if (empty($data['test'])) {
            throw new RuntimeException('Неверное JSON сообщение');
        }

        $message = new TestRabbitCustomJson($data['test']);

        return new Envelope($message, []);
    }

    /**
     * @param  Envelope  $envelope
     *
     * @return array
     * @throws JsonException
     */
    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        if ($message instanceof TestRabbitCustomJson) {
            $data = ['test' => $message->getTest()];
        } else {
            throw new RuntimeException('Не поддерживаемый класс сообщения');
        }

        return [
            'body'    => json_encode($data, JSON_THROW_ON_ERROR),
            'headers' => [],
        ];
    }
}