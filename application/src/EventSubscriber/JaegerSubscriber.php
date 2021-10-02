<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Tracer\Tracer;

class JaegerSubscriber implements EventSubscriberInterface
{
    private Tracer $tracer;
    private string $service_name;
    private string $host;
    private string $port;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->service_name = $parameterBag->get('app.name');
        $this->host = $parameterBag->get('tracing.host');
        $this->port = $parameterBag->get('tracing.port');
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $request_id = $request->attributes
            ->get('request_id');
        $parent_span_id = (int) $request->attributes
            ->get('parent_span_id', '0');

        $this->tracer = new Tracer(
            $this->service_name,
            $this->host,
            $this->port,
            $request_id,
            $parent_span_id
        );

        $tracerId = $this->tracer
            ->getSpanId();

        $request->attributes
            ->set('tracer_id', $tracerId);
    }

    public function onKernelResponse(): void
    {
        if (!empty($this->tracer)) {
            $this->tracer
                ->end();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST  => ['onKernelRequest', 40],
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}