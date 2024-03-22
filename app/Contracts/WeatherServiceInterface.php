<?php

namespace App\Contracts;

interface WeatherServiceInterface
{
    /**
     * Get the weather data for a location and date.
     *
     * @param string $location
     * @param string $date
     * @return array
     */
    public function getWeatherData(string $location, string $date): array;
}