<?php

class Booking
{
    private string $name;
    private string $service;
    private string $photographer;
    private string $date;
    private int $userId;

    public function __construct(string $name, string $service, string $photographer, string $date, int $userId)
    {
        $this->name = $name;
        $this->service = $service;
        $this->photographer = $photographer;
        $this->date = $date;
        $this->userId = $userId;
    }

    public function getName(): string { return $this->name; }
    public function getService(): string { return $this->service; }
    public function getPhotographer(): string { return $this->photographer; }
    public function getDate(): string { return $this->date; }
    public function getUserId(): int { return $this->userId; }
}