<?php
/**
 * Created by PhpStorm.
 * User: viola
 * Date: 2019-04-29
 * Time: 09:38
 */

namespace App;


use Throwable;

class PermissionException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}