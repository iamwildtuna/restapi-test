<?php
declare(strict_types=1);

namespace App\RabbitMessage;

use Ramsey\Uuid\Uuid;
use App\Traits\Interfaces\SetPropertiesInterface;
use App\Traits\SetPropertiesTrait;

/**
 * Класс для генерации пользовательских логов.
 * @property string $id - uuid лога
 * @property int $step - этап в рамках одного лога.
 * @property string $event - событие, латиницей.
 * @property string $title - заголовок события.
 * @property string $session - значение jti, может быть пустым, если логи без сессии.
 * @property int $status - статус отправки, 0 - ошибка, 1 - успешно
 * @property array $data - статус отправки, 0 - ошибка, 1 - успешно
 * @property int $userId - id пользователя, которому принадлежат логи.
 */
abstract class AbstractEventLog implements SetPropertiesInterface
{
    use SetPropertiesTrait;

    protected string $id;
    protected int $step = 1;
    protected string $event = '';
    protected string $title = '';
    protected string $session = '';
    protected int $status = 0;
    protected array $data = [];
    protected int $userId = 0;

    public function __construct()
    {
        $this->id = (string) Uuid::uuid4();
    }

    /* Setters */
    public function setId(string $id): AbstractEventLog
    {
        $this->id = $id;

        return $this;
    }

    public function setStep(int $step): AbstractEventLog
    {
        $this->step = $step;

        return $this;
    }

    public function setEvent(string $event): AbstractEventLog
    {
        $this->event = $event;

        return $this;
    }

    public function setTitle(string $title): AbstractEventLog
    {
        $this->title = $title;

        return $this;
    }

    public function setSession(string $session): AbstractEventLog
    {
        $this->session = $session;

        return $this;
    }

    public function setStatus(int $status): AbstractEventLog
    {
        $this->status = $status;

        return $this;
    }

    public function setData(array $data): AbstractEventLog
    {
        $this->data = $data;

        return $this;
    }

    public function setUserId(int $userId): AbstractEventLog
    {
        $this->userId = $userId;

        return $this;
    }

    /* Getters */
    public function getId(): string
    {
        return $this->id;
    }

    public function getStep(): int
    {
        return $this->step;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSession(): string
    {
        return $this->session;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}