<?php

namespace App\Services;

use App\Contracts\WeatherServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherApiService implements WeatherServiceInterface
{
    /**
     * Get the weather data for a location and date.
     *
     * @param string $location
     * @param string $date
     * @return array
     */
    public function getWeatherData(string $location, string $date): array
    {
        $cacheKey = "weather.{$location}.{$date}";

        return Cache::remember($cacheKey, 86400, function () use($location) {
            try {
                $response = Http::get('http://api.weatherapi.com/v1/forecast.json', [
                    'key' => config('services.weatherapi.key'),
                    'q' => $location,
                    'days' => 1,
                ]);

                $weatherConditions = [
                    'weather' => $response['current']['condition']['text'],
                    'temperature' => $response['current']['temp_c'],
                    'perciptation' => $response['current']['precip_mm'],
                ];

                return $weatherConditions;
            } catch (\Exception $e) {
                return ['error' => 'Failed to fetch weather data'];
            }
        });
    }
}