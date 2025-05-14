<?php


class Photographer
{
    private string $name;

    public function __construct(string $name)
    {
        $validPhotographers = [
            'Анна Иванова',
            'Игорь Петров',
            'Екатерина Смирнова',
        ];

        if (!in_array($name, $validPhotographers, true)) {
            throw new InvalidArgumentException('Невалидный фотограф');
        }

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function getAvailablePhotographers(): array
    {
        return [
            'Анна Иванова',
            'Игорь Петров',
            'Екатерина Смирнова',
        ];
    }
}