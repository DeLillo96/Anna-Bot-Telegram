<?php

namespace Anna\Repository;

use Anna\Utility\PostgreSQLConnector;

class UserRepository extends PostgreSQLConnector
{
    protected $tableName = 'users';
    protected $sql =
        'select
            Id,
            first_name,
            last_name,
            username
        from
            users
        ';
}