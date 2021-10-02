<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use Ramsey\Uuid\Uuid;
use App\Convert\Headers;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class HeaderConvertSubscriber implements EventSubscriberInterface
{
    /**
     * @param  RequestEvent  $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $headers = $request->headers->all();
        $parameters = (new Headers)->getParameters($headers);

        $parameters['request_id'] = !empty($parameters['request_id']) ? $parameters['request_id'] :  (string) Uuid::uuid4();

        foreach ($parameters as $key => $value) {
            $request->attributes
                ->set($key, $value);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST  => ['onKernelRequest', 60],
        ];
    }
}