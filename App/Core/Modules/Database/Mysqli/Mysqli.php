<?php
namespace App\Core\Modules\Database\Mysqli;

use App\Core\Interfaces\DatabaseInterface;
use Exception;
use PDO;

class Mysqli implements DatabaseInterface
{
    private PDO $connection;

    public function __construct(array $config)
    {
        $this->connection = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['name'] . ';charset=' . $config['charset'], $config['user'], $config['password']);
    }

    public function selectQuery(string $query, array $params)
    {
        $statement = $this->connection->prepare($query);
        if ($statement->execute($params)) {
            return $statement->fetchAll();
        } else {
            throw new Exception(sprintf('Error while executing query %s: %s', $query, print_r($statement->errorInfo(), true)));
        }
    }

    public function updateQuery(string $query, array $params)
    {var_dump([$query, $params]);
        $statement = $this->connection->prepare($query);
        if ($statement->execute($params)) {
            return $this->connection->lastInsertId();
        } else {
            throw new Exception(sprintf('Error while executing query %s: %s', $query, print_r($statement->errorInfo(), true)));
        }
    }
}