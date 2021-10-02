<?php
declare(strict_types=1);

namespace App\Tracer;

use Exception;
use Jaeger\Config;
use Jaeger\Jaeger;
use Jaeger\Scope;
use Jaeger\SpanContext;
use RuntimeException;

class Tracer
{
    private string $service_name;
    private string $host;
    private string $port;
    private Config $config;
    private Jaeger $tracer;
    private ?SpanContext $initSpanContext = null;
    private array $scopes = [];
    private Scope $currentScope;

    /**
     * Tracer constructor.
     *
     * @param  string  $name
     * @param  string  $host
     * @param  string  $port
     * @param  string  $request_id
     * @param  int  $parent_span_id
     */
    public function __construct(string $name, string $host, string $port, string $request_id = '', int $parent_span_id = 0)
    {
        $this->host = $host;
        $this->port = $port;
        $this->service_name = $name;

        $this->setConfig();
        $this->setTracer();

        $this->setParentSpanContext($parent_span_id);

        $options = [];
        if ($this->initSpanContext !== null) {
            $options = ['child_of' => $this->initSpanContext];
        }

        $this->config
            ->gen128bit();

        $span_name = $this->service_name.'-span';
        $this->start($span_name, $options);

        $tagRequestId = new Tag('requestId', $request_id);
        $this->addTag($tagRequestId);
    }

    /**
     * Начать span, все спаны содержат tag  своим id.
     *
     * @param  string  $name
     * @param  array  $options
     */
    public function start(string $name, array $options = []): void
    {
        $this->currentScope = $this->tracer
            ->startActiveSpan($name, $options);

        $tagSpanId = new Tag('span.id', $this->getSpanId());
        $this->addTag($tagSpanId);

        $this->scopes[] = $this->currentScope;
    }

    /**
     * Добавить tag/tags к текущему открытому span
     *
     * @param  Tag  ...$tags
     *
     * @return $this
     */
    public function addTag(Tag ...$tags): Tracer
    {
        foreach ($tags as $tag) {
            $this->currentScope
                ->getSpan()
                ->setTag($tag->getKey(), $tag->getValue());
        }

        return $this;
    }

    /**
     * Добавить логи к текущему открытому span
     *
     * @param  array  $fields
     * @param  int|null  $timestamp
     *
     * @return $this
     */
    public function addLog(array $fields, ?int $timestamp = null): Tracer
    {
        $this->currentScope
            ->getSpan()
            ->log($fields, $timestamp);

        return $this;
    }

    /**
     * Закрыть текущий span
     *
     * @return void
     */
    public function stop(): void
    {
        $this->currentScope
            ->close();

        array_pop($this->scopes);
    }

    /**
     * Чтобы сообщить трасеровщику, конец родительского scope;
     * !TODO Возможно будет уместно использовать __destruct
     *
     * @return void
     */
    public function end(): void
    {
        $this->config
            ->flush();
    }

    /**
     * Получение spanId для последующей установки в реквест.
     *
     * @return string
     */
    public function getSpanId(): string
    {
        /** @var SpanContext $spanContext */
        $spanContext = $this->currentScope
            ->getSpan()
            ->spanContext;

        return $spanContext->spanId;
    }

    /** Служебные */
    /**
     * Устанавливает config Jaeger
     *
     * @return void
     */
    private function setConfig(): void
    {
        $config = Config::getInstance();

        if (is_null($config)) {
            throw new RuntimeException('Jaeger config, проблема с инициализацией.');
        }

        $this->config = $config;
    }

    /**
     * Устанавливает Jaeger tracer
     *
     * @return void
     */
    private function setTracer(): void
    {
        try {
            $tracer = $this->config
                ->initTracer($this->service_name, $this->host.':'.$this->port);
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }

        if (is_null($tracer)) {
            throw new RuntimeException('Jaeger tracer не подключен, проблема с инициализацией.');
        }

        $this->tracer = $tracer;
    }

    /**
     * Устанавливает родительский spanContext для инициализации, родительского контекста.
     *
     * @param  int  $parent_span_id
     */
    private function setParentSpanContext(int $parent_span_id): void
    {
        if ($parent_span_id > 0) {
            $this->initSpanContext = new SpanContext(
                $parent_span_id,
                null,
                true,
            );

            $this->initSpanContext
                ->traceIdLow = $parent_span_id;
        }
    }
}