<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/15
 * Time: 15:49
 */

namespace MdDoc;

use RuntimeException;
use Throwable;

/**
 * 解析异常
 * Class ParserException
 * @package MdDoc
 */
class ParserException extends RuntimeException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
