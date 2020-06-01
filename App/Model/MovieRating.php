<?php


namespace App\Model;


use App\Core\Interfaces\ModelInterface;
use DateTimeInterface;

class MovieRating implements ModelInterface
{
    private ?int $id = null;
    private int $movieId;
    private DateTimeInterface $date;
    private float $rating;
    private int $position;
    private int $votersCount;

    private array $dataMapping = [
        'id' => [
            'propertyName' => 'id',
            'type' => 'int'
        ],
        'movie_id' => [
            'propertyName' => 'movieId',
            'type' => 'int'
        ],
        'date' => [
            'propertyName' => 'date',
            'type' => 'date'
        ],
        'rating' => [
            'propertyName' => 'rating',
            'type' => 'float'
        ],
        'position' => [
            'propertyName' => 'position',
            'type' => 'int'
        ],
        'voters_count' => [
            'propertyName' => 'votersCount',
            'type' => 'int'
        ],
    ];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getMovieId(): int
    {
        return $this->movieId;
    }

    public function setMovieId(int $movieId): self
    {
        $this->movieId = $movieId;
        return $this;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getVotersCount(): int
    {
        return $this->votersCount;
    }

    public function setVotersCount(int $votersCount): self
    {
        $this->votersCount = $votersCount;
        return $this;
    }

    public function getDataMapping(): array
    {
        return $this->dataMapping;
    }
}