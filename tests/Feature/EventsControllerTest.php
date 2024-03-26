<?php

namespace Tests\Feature;

use App\Contracts\WeatherServiceInterface;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class EventsControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $weatherService;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        // Mock the WeatherServiceInterface
        $this->weatherService = Mockery::mock(WeatherServiceInterface::class);
        $this->app->instance(WeatherServiceInterface::class, $this->weatherService);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testIndexReturnsListOfEvents()
    {
        $user = User::factory()->create();
        Event::factory()->count(5)->create(['user_id' => $user->id]);

        $token = $user->createToken('TestToken')->plainTextToken;
        $user->events()
            ->with('invitees')
            ->orderBy('start_time')
            ->paginate(5);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson(route('events.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'events',
            'total',
            'per_page',
            'current_page',
        ]);
        $response->assertJsonCount(5, 'events');
    }

    public function testShowReturnsEventDetails()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;
        $event = Event::factory()->create(['user_id' => $user->id]);

        $this->weatherService->shouldReceive('getWeatherData')
            ->andReturn([
                'forecast' => 'Sunny',
                'temperature' => 25,
                'percipitation' => 0,
            ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson(route('events.show', ['event' => $event->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'title',
            'start_time',
            'end_time',
            'description',
            'location',
            'user_id',
            'invitees',
            'weather',
        ]);
    }

    public function testStoreCreatesNewEvent()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $event = Event::factory()->make(['user_id' => $user->id]);
        $this->weatherService->shouldReceive('getWeatherData')
            ->andReturn([
                'forecast' => 'Sunny',
                'temperature' => 25,
                'percipitation' => 0,
            ]);

        $event_data = [
            'title' => $event->title,
            'start_time' => $event->start_time,
            'end_time' => $event->end_time,
            'description' => $event->description,
            'location' => $event->location,
            'invitees' => [],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('events.store'), $event_data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'title',
            'start_time',
            'end_time',
            'description',
            'location',
            'user_id',
            'invitees',
            'weather',
        ]);
    }

    public function testDestroyDeletesEvent()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $event = Event::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->deleteJson(route('events.destroy', ['event' => $event->id]));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function testUpdateUpdatesEvent()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $event = Event::factory()->create(['user_id' => $user->id]);

        $event_data = [
            'title' => 'Updated Event Title',
            'start_time' => '2021-12-31 23:59:59',
            'end_time' => '2022-01-01 00:00:00',
            'description' => 'Updated Event Description',
            'location' => 'Podgorica',
            'invitees' => [],
        ];

        $this->weatherService->shouldReceive('getWeatherData')
            ->andReturn([
                'forecast' => 'Sunny',
                'temperature' => 25,
                'percipitation' => 0,
            ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->putJson(route('events.update', ['event' => $event->id]), $event_data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'title',
            'start_time',
            'end_time',
            'description',
            'location',
            'user_id',
        ]);
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Event Title',
            'start_time' => '2021-12-31 23:59:59',
            'end_time' => '2022-01-01 00:00:00',
            'description' => 'Updated Event Description',
            'location' => 'Podgorica',
        ]);
    }

    public function testLocationsReturnsEventLocations()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        Event::factory()->count(5)->create(['user_id' => $user->id]);

        $this->weatherService->shouldReceive('getWeatherData')
            ->andReturn([
                'forecast' => 'Sunny',
                'temperature' => 25,
                'percipitation' => 0,
            ])->times(5);

        $locations = $user->events()
            ->orderBy('start_time')
            ->select('start_time', 'end_time', 'location')
            ->distinct()
            ->get('location', 'start_time', 'end_time');

        foreach ($locations as $location) {
            $locations_data[] = [
                'location' => $location->location,
                'start_time' => $location->start_time,
                'end_time' => $location->end_time,
                'weather_conditions' => [
                    'forecast' => 'Sunny',
                    'temperature' => 25,
                    'percipitation' => 0,
                ],
            ];
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson(route('events.locations'));

        $response->assertStatus(200);
        $response->assertJson($locations_data);
    }

    public function testIndexReturnsUnauthorized()
    {
        $response = $this->getJson(route('events.index'));
        $response->assertStatus(401);
    }

    public function testShowReturnsUnauthorized()
    {
        $event = Event::factory()->create();

        $response = $this->getJson(route('events.show', ['event' => $event->id]));
        $response->assertStatus(401);
    }

    public function testStoreReturnsUnauthorized()
    {
        $event = Event::factory()->make();

        $response = $this->postJson(route('events.store'), $event->toArray());
        $response->assertStatus(401);
    }

    public function testDestroyReturnsUnauthorized()
    {
        $event = Event::factory()->create();

        $response = $this->deleteJson(route('events.destroy', ['event' => $event->id]));
        $response->assertStatus(401);
    }

    public function testUpdateReturnsUnauthorized()
    {
        $event = Event::factory()->create();

        $response = $this->putJson(route('events.update', ['event' => $event->id]), $event->toArray());
        $response->assertStatus(401);
    }

    public function testLocationsReturnsUnauthorized()
    {
        $response = $this->getJson(route('events.locations'));
        $response->assertStatus(401);
    }
}
