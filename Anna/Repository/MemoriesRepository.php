<?php

namespace Anna\Repository;

use Anna\Utility\PostgreSQLConnector;

class MemoriesRepository extends PostgreSQLConnector
{
    protected $tableName = 'memory';
    protected $sql =
        'select
            memory.id as memory_id,
            user_id,
            text,
            username,
            chat_id
        from
            memory left join users on memory.user_id = users.id
        ';

    public function create(array $data)
    {
        $userRepository = new UserRepository();
        $userResult = $userRepository->read([
            'username' => $data['username'],
            'chat_id' => $data['chat_id']
        ]);

        return parent::create([
            'text' => $data['text'],
            'user_id' => array_shift($userResult)['id']
        ]);
    }

    public function delete($params)
    {
        $userRepository = new UserRepository();
        $userResult = $userRepository->read([
            'username' => $params['username'],
            'chat_id' => intval($params['chat_id'])
        ]);

        $memories = $this->read([
            'text' => '%' . $params['text'] . '%',
            'user_id' => intval(array_shift($userResult)['id'])
        ]);

        foreach ($memories as $memory) {
            if(!parent::delete(['id' => $memory['memory_id']])) {
                return false;
            }
        }
        return true;
    }
}