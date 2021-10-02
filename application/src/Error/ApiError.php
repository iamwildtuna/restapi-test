<?php
declare(strict_types=1);

namespace App\Error;

use InvalidArgumentException;

class ApiError
{
    public const VALIDATION_CODE = 10000;
    public const TOKEN_NO_PASSED = 10001;
    public const TOKEN_INVALID = 10002;
    public const TOKEN_EXPIRED = 10003;
    public const TOKEN_PUBLISHER_NOT_FOUND = 10004;
    public const TOKEN_REVOKED = 10005;
    public const TOKEN_INTERCEPTED = 10006;
    public const JSON_SYNTAX_ERROR = 10007;
    public const REQUEST_INVALID_JSON_FORMAT = 10008;
    public const REQUEST_NO_UNICODE = 10009;
    public const JSON_INFINITE_LOOP = 10010;
    public const JSON_NAME_ENCODE_ERROR = 10011;
    public const PROPERTY_NOT_FOUND = 10012;
    public const NOT_FOUND = 10013;
    public const ORM_CLEAR_ERROR = 10014;
    public const ORM_CONNECTION_ERROR = 10015;
    public const INTERNAL_SERVER_ERROR = 10016;
    public const API_NO_METHOD_FOUND = 10017;
    public const API_METHOD_NOT_SUPPORTED = 10018;
    public const ORM_EXCEPTION = 10019;
    public const ORM_OPTIMISTIC_LOCK = 10020;
    public const OBJECT_INSTANCE_ERROR = 10021;
    public const NOT_POSSIBLE_CREATE_ENTROPY = 10022;
    private static array $defaultCodeList = [
        self::API_NO_METHOD_FOUND         => 'Вызываемая функция отсутствует в API',
        self::API_METHOD_NOT_SUPPORTED    => 'Вызываемая функция не поддерживает переданный метод запроса. Попробуйте метод: %s',
        self::INTERNAL_SERVER_ERROR       => 'Внутренняя ошибка сервиса. Обратитесь в службу поддержки.',
        self::JSON_INFINITE_LOOP          => 'Обнаружен бесконечный цикл в преобразуемом объекте. Проверьте корректность JSON запроса',
        self::JSON_NAME_ENCODE_ERROR      => 'Имя свойства не может быть закодировано в JSON запросе',
        self::JSON_SYNTAX_ERROR           => 'Синтаксическая ошибка в JSON запросе',
        self::NOT_FOUND                   => '%s - не найден',
        self::OBJECT_INSTANCE_ERROR       => 'Ошибка инстанцирования, ожидается объект типа %s',
        self::PROPERTY_NOT_FOUND          => 'Свойство: %s не найдено у объекта',
        self::ORM_CLEAR_ERROR             => 'Не получилось отчистить persist модель.',
        self::ORM_CONNECTION_ERROR        => 'Проблемы с подтверждением транзакции',
        self::ORM_EXCEPTION               => 'База данных не отвечает',
        self::ORM_OPTIMISTIC_LOCK         => 'Сбой при проверке версии объекта',
        self::REQUEST_INVALID_JSON_FORMAT => 'Запрос должен быть в формате JSON',
        self::REQUEST_NO_UNICODE          => 'Запрос должен быть в кодировке UTF-8. Многобайтовые символы должны быть преобразованы в Unicode',
        self::TOKEN_NO_PASSED             => 'Не передан токен',
        self::TOKEN_INVALID               => 'Неверный токен',
        self::TOKEN_EXPIRED               => 'Срок действия токена истек',
        self::TOKEN_PUBLISHER_NOT_FOUND   => 'Издатель токена не найден',
        self::TOKEN_REVOKED               => 'Токен отозван',
        self::TOKEN_INTERCEPTED           => 'Возможно токен перехвачен',
        self::VALIDATION_CODE             => 'Ошибка валидации данных',
        self::NOT_POSSIBLE_CREATE_ENTROPY => 'Не получилось собрать достаточную энтропию',
    ];
    protected static array $codeList = [];

    public static function getMessage(int $code, string ...$values): string
    {
        /** @noinspection AdditionOperationOnArraysInspection */
        $codeList = static::$defaultCodeList + static::$codeList;

        if (empty($codeList[$code])) {
            throw new InvalidArgumentException('Передан несуществующий код ошибки API: '.$code);
        }

        return sprintf($codeList[$code], ...$values);
    }
}