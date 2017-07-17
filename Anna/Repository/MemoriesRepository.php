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

    public function create(array $data)
    {
        $memoryResult = parent::create([
            'text' => $data['text']
        ]);

        $userRepository = new UserRepository();
        $userResult = $userRepository->read([
            'username' => $data['username'],
            'chat_id' => $data['chat_id']
        ]);

        error_log(implode("|",$memoryResult));
        die;
        $crateString = "insert into user_memory (user_id, memory_id) values (" .
            array_shift($userResult)['id'] . ", " . array_shift($memoryResult)['id'] . ");";

        return pg_query($this->connection, $crateString);
    }
}