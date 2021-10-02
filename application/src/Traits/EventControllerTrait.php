<?php
declare(strict_types=1);

namespace App\Traits;

use App\RabbitMessage\AbstractEventLog;
use App\Security\User;
use Symfony\Component\Security\Core\Security;

trait EventControllerTrait
{
    protected AbstractEventLog $event;

    protected function setEventDefault(Security $security, AbstractEventLog $event): void
    {
        /** @var User $user */
        $user = $security->getUser();
        $userSession = $user ? $user->getSession() : '';
        $userId = $user ? $user->getId() : 0;

        $this->event = $event->setSession($userSession)
            ->setUserId($userId);
    }

    /**
     * @param  int  $status
     * @return $this
     */
    protected function setEventStatus(int $status = 0): self
    {
        if ($status === 1) {
            $this->event
                ->setStatus($status);
        }

        return $this;
    }

    /**
     * Устанавливает значения для события
     *
     * @param  string  $event
     * @param  string  $title
     * @param  array  $data
     */
    protected function setEventProperties(string $event, string $title, array $data = []): void
    {
        $this->event
            ->setProperties([
                'event' => $event,
                'title' => $title,
                'data'  => $data,
            ]);
    }
}