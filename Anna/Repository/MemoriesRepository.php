<?php

namespace Anna\Repository;

use Anna\Utility\PostgreSQLConnector;

class MemoriesRepository extends PostgreSQLConnector
{
    protected $tableName = 'memory';
    protected $sql =
        'select
            text,
            username,
            chat_id
        from
            memory
            left join user_memory on memory.id = user_memory.memory_id
            left join users on user_memory.user_id = users.id
        ';
}