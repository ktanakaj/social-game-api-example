<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Validation\ValidationException;

/**
 * 汎用の業務エラー例外クラス。
 */
class AppException extends \Exception
{
    /**
     * エラーごとの追加情報。
     * @var mixed
     */
    protected $data;

    /**
     * 例外を生成する。
     * @param string $message エラーメッセージ。
     * @param string $code エラーコード。
     * @param mixed $data 追加のエラー情報。
     * @param \Throwable $previous 元となった例外。
     */
    public function __construct(string $message, string $code, $data = null, \Throwable $previous = null)
    {
        // ※ 引数でcodeを渡すと型エラーとなるが、プロパティに直接詰めるのは大丈夫なのでそうしている
        $this->code = $code;
        parent::__construct($message, 0, $previous);
        $this->data = $data;
    }

    /**
     * エラーごとの追加情報を取得する。
     * @return mixed 追加情報。
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 各種の例外をAppExceptionに変換する。
     * @param \Throwable $ex 変換元の例外。
     * @return AppException 変換後の例外。
     */
    public static function convert(\Throwable $ex) : AppException
    {
        // 例外クラスまたは例外名を元に各エラーの内容に合わせて変換
        if ($ex instanceof AppException) {
            return $ex;
        } elseif ($ex instanceof MaintenanceModeException) {
            return new AppException($ex->getMessage(), 'MAINTENANCE_MODE', null, $ex);
        } elseif ($ex instanceof HttpException) {
            return self::fromHttpException($ex);
        } elseif ($ex instanceof AuthenticationException) {
            return new UnauthorizedException($ex->getMessage());
        } elseif ($ex instanceof ModelNotFoundException) {
            return new NotFoundException($ex->getMessage());
        } elseif ($ex instanceof ValidationException) {
            return self::fromValidationException($ex);
        }

        // その他のエラーは、サーバーエラーに変換
        return new InternalServerErrorException($ex->getMessage(), $ex);
    }

    /**
     * SymfonyのHttpExceptionを変換する。
     * @param HttpException $ex 変換元の例外。
     * @return AppException 変換後の例外。
     */
    private static function fromHttpException(HttpException $ex) : AppException
    {
        $msg = $ex->getMessage();
        switch ($ex->getStatusCode()) {
            // ※ 簡易的にいくつかの4xxエラーは400にまとめている
            case 400:
            case 406:
            case 413:
            case 422:
                return new BadRequestException($msg, $ex);
            case 401:
                return new UnauthorizedException($msg);
            case 403:
                return new ForbiddenException($msg);
            case 404:
                return new NotFoundException($msg, $ex);
            case 405:
                return new AppException($msg, 'METHOD_NOT_ALLOWED', null, $ex);
            case 408:
                return new AppException($msg, 'REQUEST_TIMEOUT', null, $ex);
            case 409:
                return new ConflictException($msg, $ex);
            case 429:
                return new AppException($msg, 'TOO_MANY_REQUESTS', null, $ex);
            case 501:
                return new AppException($msg, 'NOT_IMPLEMENTED', null, $ex);
            case 503:
                return new AppException($msg, 'SERVICE_UNAVAILABLE', null, $ex);
            default:
                return new InternalServerErrorException($msg, $ex);
        }
    }

    /**
     * Laravelのバリデーションエラーを変換する。
     * @param ValidationException $ex 変換元の例外。
     * @return AppException 変換後の例外。
     */
    private static function fromValidationException(ValidationException $ex) : AppException
    {
        // メッセージだけだと分かり辛いので、バリデーション情報もメッセージにマージする
        return new BadRequestException($ex->getMessage() . ' (' . implode(' ', array_map(function ($p) {
            return implode(' ', $p);
        }, $ex->errors())) . ')');
    }
}
