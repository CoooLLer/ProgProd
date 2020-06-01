<?php


namespace App\Model;


use App\Core\Interfaces\ModelInterface;

class Movie implements ModelInterface
{
    private ?int $id = null;
    private string $name;
    private int $year;
    private string $picture;
    private array $dataMapping = [
        'id' => [
            'propertyName' => 'id',
            'type' => 'int'
        ],
        'name' => [
            'propertyName' => 'name',
            'type' => 'string'
        ],
        'year' => [
            'propertyName' => 'year',
            'type' => 'int'
        ],
        'picture' => [
            'propertyName' => 'picture',
            'type' => 'string'
        ],
    ];

    public function getDataMapping(): array
    {
        return $this->dataMapping;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getPicture(): string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;
        return $this;
    }
}