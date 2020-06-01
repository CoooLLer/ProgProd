<?php


namespace App\Core\Interfaces;


interface ModelInterface
{
    public function getId(): ?int;

    public function getDataMapping(): array;
}