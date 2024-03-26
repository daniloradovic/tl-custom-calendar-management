<?php

namespace Tests\Unit\Services;

use App\Services\WeatherApiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherApiServiceTest extends TestCase
{
    public function testGetWeatherDataReturnsWeatherConditions()
    {
        // Mock the Http facade
        Http::shouldReceive('get')->once()->andReturn([
            'current' => [
                'condition' => [
                    'text' => 'Sunny',
                ],
                'temp_c' => 25,
                'precip_mm' => 0,
            ],
        ]);

        // Mock the Cache facade
        Cache::shouldReceive('remember')->once()->andReturnUsing(function ($cacheKey, $ttl, $callback) {
            return $callback();
        });

        // Create an instance of the WeatherApiService
        $weatherService = new WeatherApiService();

        // Call the getWeatherData method
        $weatherData = $weatherService->getWeatherData('Podgorica', '2021-12-31');

        // Assert the returned weather data
        $this->assertEquals([
            'forecast' => 'Sunny',
            'temperature' => 25,
            'perciptation' => 0,
        ], $weatherData);
    }

    public function testGetWeatherDataReturnsErrorOnException()
    {
        // Mock the Http facade to throw an exception
        Http::shouldReceive('get')->once()->andThrow(new \Exception('Failed to fetch weather data'));

        // Mock the Cache facade
        Cache::shouldReceive('remember')->once()->andReturnUsing(function ($cacheKey, $ttl, $callback) {
            return $callback();
        });

        // Create an instance of the WeatherApiService
        $weatherService = new WeatherApiService();

        // Call the getWeatherData method
        $weatherData = $weatherService->getWeatherData('Podgorica', '2021-12-31');

        // Assert the returned error message
        $this->assertEquals(['error' => 'Failed to fetch weather data'], $weatherData);
    }
}
