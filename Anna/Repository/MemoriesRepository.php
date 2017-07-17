<?php

namespace Anna\Repository;

use Anna\Utility\PostgreSQLConnector;

class MemoriesRepository extends PostgreSQLConnector
{
    protected $tableName = 'memory';
    protected $sql =
        'select
            memory.id as memory_id,
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
        parent::create([
            'text' => $data['text']
        ]);

        $userRepository = new UserRepository();
        $userResult = $userRepository->read([
            'username' => $data['username'],
            'chat_id' => $data['chat_id']
        ]);

        $memoryResult = $this->read([
            'text' => $data['text']
        ]);
        error_log(var_dump($memoryResult));
        $crateString = "insert into user_memory (user_id, memory_id) values (" .
            array_shift($userResult)['id'] . ", " . array_shift($memoryResult)['memory_id'] . ");";

        return pg_query($this->connection, $crateString);
    }
}