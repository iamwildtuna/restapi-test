<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;
use App\Error\ApiError;
use Throwable;

class ApiException extends Exception
{
    protected string $messageText;

    /**
     * ApiException constructor.
     *
     * @param  int  $code
     * @param  String[]  $values
     * @param  Throwable|null  $previous
     */
    final public function __construct($code = 0, array $values = [], ?Throwable $previous = null)
    {
        $this->setMessageText($code, $values);
        parent::__construct($this->messageText, $code, $previous);
    }

    protected function setMessageText($code, $values): void
    {
        $this->messageText = ApiError::getMessage($code, ...$values);
    }
}