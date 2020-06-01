<?php


namespace App\Repository;


use App\Core\AbstractClasses\ModelRepository;
use App\Core\Interfaces\DatabaseInterface;
use App\Model\Movie;

class MovieRepository extends ModelRepository
{
    public function __construct(DatabaseInterface $database)
    {
        parent::__construct($database);
        $this->baseClass = Movie::class;
        $this->tableName = 'movie';
    }
}