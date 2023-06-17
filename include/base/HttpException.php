<?php
/**
 * HttpException.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014. Segworks Technologies Corporation
 */

namespace Segworks\HIS\Base;

/**
 * Description
 *
 * @package Segworks.HIS.
 */
class HttpException extends ErrorException
{
    /**
     * @var int The HTTP error code
     */
    public $statusCode = 200;

    /**
     * Constructor.
     * @param integer $status HTTP status code, such as 404, 500, etc.
     * @param string $message error message
     * @param integer $code error code
     */
    public function __construct($status, $message=null, $code=0)
    {
        $this->statusCode = $status;
        parent::__construct($message, $code);
    }
}