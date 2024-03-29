<?php

namespace Anna\Utility;

class PostgreSQLConnector
{
    protected $connectionString = "CONNECTION_STRING";
    protected $connection;
    protected $sql = '';
    protected $tableName = '';

    public function __construct($connectionString = false)
    {
        if($connectionString) $this->connectionString = $connectionString;
        $this->connection = pg_connect($this->connectionString);
    }

    public function read($params = [])
    {
        $result = pg_query($this->connection, $this->prepareQuery($params));
        return pg_fetch_all($result);
    }

    public function delete($params)
    {
        return pg_delete($this->connection, $this->tableName, $params);
    }

    public function create(array $data)
    {
        $keys = [];
        $values = [];
        foreach ($data as $key => $value) {
            array_push($keys, $key);
            array_push($values, "'" . $value . "'");
        }
        $crateString = "insert into " . $this->tableName . ' (' . implode(",", $keys) . ') ' .
            'values (' . implode(",", $values) . ');';

        $result = pg_query($this->connection, $crateString);
        return $result;
    }

    protected function prepareQuery(array $params)
    {
        if(empty($params)) return $this->sql;
        $query = 'SELECT * FROM (' . $this->sql . ') as t WHERE ';
        $where = '';
        foreach ($params as $key => $param){
            if(is_string($param)){
                $query .= $where . " " . $key . " like '" . $param . "'";
            } else {
                $query .= $where . " " . $key . " = " . $param;
            }
            $where = ' AND ';
        }
        return $query;
    }
}