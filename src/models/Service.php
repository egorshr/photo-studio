<?php


class Service
{
    private string $name;

    public function __construct(string $name)
    {
        $validServices = [
            'Портретная съёмка',
            'Семейная фотосессия',
            'Съёмка на документы',
            'Творческая съёмка',
        ];

        if (!in_array($name, $validServices, true)) {
            throw new \InvalidArgumentException('Невалидная услуга');
        }

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function getAvailableServices(): array
    {
        return [
            'Портретная съёмка',
            'Семейная фотосессия',
            'Съёмка на документы',
            'Творческая съёмка',
        ];
    }
}
