<?php


namespace App\Repository;


use App\Core\AbstractClasses\ModelRepository;
use App\Core\Interfaces\DatabaseInterface;
use App\Model\Movie;
use App\Model\MovieRating;
use DateTimeInterface;

class MovieRatingRepository extends ModelRepository
{
    public function __construct(DatabaseInterface $database)
    {
        parent::__construct($database);
        $this->baseClass = MovieRating::class;
        $this->tableName = 'movie_rating';
    }

    /**
     * Присутствует связность данных, завязываемся на конкретную сущность,
     * по хорошему завести интерфейс и плясать от него, но в рамках тестового задания можно немного отступить из-за размеров системы
     * @param Movie $movie
     * @param DateTimeInterface $date
     */
    public function getForMovieOnDate(Movie $movie, DateTimeInterface $date)
    {
        return $this->getBy(['movie_id' => $movie->getId(), 'date' => $date]);
    }
}