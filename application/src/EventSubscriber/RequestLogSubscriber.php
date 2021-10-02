<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use Exception;
use App\Convert\Headers;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Psr\Log\LoggerInterface;

class RequestLogSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $gelfLogger)
    {
        $this->logger = $gelfLogger;
    }

    /**
     * @param  RequestEvent  $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $request_id = $request->attributes
            ->get('request_id');

        try {
            $this->logger
                ->info('Request '.$request_id.': '.$request->getMethod().' '.
                    $request->getRequestUri().' '.$request->getContent());
        } catch (Exception $exception) {
            $this->logger
                ->error('Request'.$request_id.': '.$request->getMethod().' '.
                    $request->getContent().' Error: '.$exception->getMessage());
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();
        $request_id = $request->attributes
            ->get('request_id');

        $response->headers
            ->set(Headers::REQUEST_ID, $request_id);

        try {
            $this->logger
                ->info('Response '.$request_id.': '.$response->getContent());
        } catch (Exception $exception) {
            $this->logger
                ->error('Response '.$request_id.': '.$exception->getMessage());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST  => ['onKernelRequest', 50],
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}