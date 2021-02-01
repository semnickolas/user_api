<?php

namespace UserApi\Exception;

use Exception;

/**
 * Class UsersNotFoundException
 * @package UserApi\Exception
 */
class UsersNotFoundException extends Exception
{
    /**
     * @var int
     */
    protected $code = 404;

    /**
     * @var string
     */
    protected $message = 'Users not found!';
}