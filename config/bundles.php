<?php

declare(strict_types=1);

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Tui\Musement\ApiClient\TuiMusementApiClientBundle::class => ['all' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
    Tui\Weather\ApiClient\WeatherApiClientBundle::class => ['all' => true],
];
