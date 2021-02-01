<?php

namespace UserApi\Enum;

/**
 * Class ResponseEnum
 * @package UserApi\Enum
 */
class ResponseEnum
{
    public const USER_CREATED = ['data' => 'User created'];

    public const USERS_VIEW = 'users.html.twig';
    public const HTML_HEADER = ['content-type' => 'text/html'];

    public const USERS_INDEX = 'users';
    public const FILTER_INDEX = 'filter';

}