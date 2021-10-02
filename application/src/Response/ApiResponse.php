<?php
declare(strict_types=1);

namespace App\Response;

use App\Convert\Headers;
use App\Error\ApiError;
use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JsonException;
use Throwable;

class ApiResponse
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Возвращает структурированный успешный ответ
     *
     * @param  array  $data
     *
     * @return Response
     */
    public function getSuccess($data = []): Response
    {
        $result = $this->success($data);

        return $this->getResponse($result);
    }

    /**
     * Возвращает структурированный ответ с ошибкой
     *
     * @param  Throwable  $exception
     *
     * @return Response
     */
    public function getError(Throwable $exception): Response
    {
        $result = $this->error($exception);

        return $this->getResponse($result);
    }

    private function getResponse($data = []): Response
    {
        $headers = [
            Headers::REQUEST_ID => $this->request
                ->attributes
                ->get('request_id'),
        ];
        $response = new JsonResponse('', Response::HTTP_OK, $headers);

        return $response->setData($data);
    }

    private function success($data = []): array
    {
        return [
            'status'        => 'success',
            'code'          => '20000',
            'error_message' => '',
            'data'          => $data,
        ];
    }

    private function error(Throwable $exception): array
    {
        if ($exception instanceof MethodNotAllowedHttpException) {
            $headers = $exception->getHeaders();
            $code = ApiError::API_METHOD_NOT_SUPPORTED;
            $message = ApiError::getMessage($code, $headers['Allow']);

            return $this->getErrorResult($message, $code);
        }

        if ($exception instanceof NotFoundHttpException) {
            $code = ApiError::API_NO_METHOD_FOUND;

            return $this->getErrorResult(ApiError::getMessage($code), $code);
        }

        if ($exception instanceof JsonException) {
            switch ($exception->getCode()) {
                case JSON_ERROR_SYNTAX:
                    $code = ApiError::JSON_SYNTAX_ERROR;
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                case JSON_ERROR_UTF8:
                case JSON_ERROR_INF_OR_NAN:
                case JSON_ERROR_UTF16:
                    $code = ApiError::REQUEST_INVALID_JSON_FORMAT;
                    break;
                case JSON_ERROR_CTRL_CHAR:
                case JSON_ERROR_UNSUPPORTED_TYPE:
                    $code = ApiError::REQUEST_NO_UNICODE;
                    break;
                case JSON_ERROR_RECURSION:
                    $code = ApiError::JSON_INFINITE_LOOP;
                    break;
                case JSON_ERROR_INVALID_PROPERTY_NAME:
                    $code = ApiError::JSON_NAME_ENCODE_ERROR;
                    break;
                default:
                    $code = ApiError::INTERNAL_SERVER_ERROR;
            }

            return $this->getErrorResult(ApiError::getMessage($code), $code);
        }

        $code = ApiError::INTERNAL_SERVER_ERROR;

        if ($exception instanceof ApiException) {
            $code = $exception->getCode();
        }

        return $this->getErrorResult($exception->getMessage(), $code);
    }

    private function getErrorResult(string $message, int $code, array $errors = []): array
    {
        $result = [
            'status'        => 'error',
            'code'          => $code,
            'error_message' => $message,
        ];
        if ($code === ApiError::VALIDATION_CODE) {
            $result['validation_errors'] = $errors;
        }

        return $result;
    }
}