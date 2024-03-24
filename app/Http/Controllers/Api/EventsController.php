<?php

namespace App\Http\Controllers\Api;

use App\Contracts\WeatherServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Jobs\CreateInvitees;
use App\Jobs\UpdateInvitees;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class EventsController
 *
 * This class is responsible for handling API requests related to events.
 */
class EventsController extends Controller
{
    public function __construct(private WeatherServiceInterface $weatherService)
    {
    }

    /**
     * Retrieve a list of events.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->cannot('viewAny', Event::class)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $start_time = $request->query('start_time');
        $end_time = $request->query('end_time');

        if (! empty($start_time) && ! empty($end_time)) {
            if ($start_time > $end_time) {
                return response()->json(['error' => 'Invalid date range'], 400);
            }

            $events = $user->events()
                ->whereBetween('start_time', [$start_time, $end_time])
                ->orWhereBetween('end_time', [$start_time, $end_time])
                ->orWhere(function ($query) use ($start_time, $end_time) {
                    $query->where('start_time', '<', $start_time)->where('end_time', '>', $end_time);
                })
                ->get();

        } else {
            $events = $user->events()->get();
        }

        return response()->json($events, 200);
    }

    /**
     * Display the specified event.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $event = Event::find($id);
        if (! $event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        $user = Auth::user();
        if ($user->cannot('view', $event)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $weather_data = $this->weatherService->getWeatherData($event->location, $event->start_time);
        $invitees = $event->invitees()->pluck('email', 'id')->toArray();
        $event->invitees = $invitees;
        $event->weather = $weather_data;

        return response()->json($event, 200);
    }

    /**
     * Store a new event.
     *
     * @param  StoreEventRequest  $request  The request object containing the event data.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreEventRequest $request)
    {
        $user = Auth::user();
        if ($user->cannot('create', Event::class)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $invitees = $request->invitees;

        if (count($invitees) !== count(array_unique($invitees))) {
            // Duplicate values exist in the array
            $invitees = array_unique($invitees);
        }

        $event = $user->events()->create($request->validated());

        // send email to invitees in the background using a job
        CreateInvitees::dispatch($invitees, $event);

        return response()->json($event, 201);
    }

    /**
     * Delete an event.
     *
     * @param  Event  $event  The event to be deleted.
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $event = Event::find($id);
        if (! $event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        // Check if user is authorized to delete the event
        $user = Auth::user();
        if (! $user->can('delete', $event)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Soft delete the event
        $event->delete();

        return response()->json(null, 204);
    }

    /**
     * Update an event.
     *
     * @param  StoreEventRequest  $request  The request object containing the event data.
     * @param  Event  $event  The event to be updated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreEventRequest $request, Event $event)
    {
        $user = Auth::user();
        // Check if user is authorized to update the event
        if (! $user->can('update', $event)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        UpdateInvitees::dispatch($event, $request->invitees);

        $event->update($request->validated());

        return response()->json($event, 200);
    }

    /**
     * Retrieve the locations for the events.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function locations(Request $request)
    {
        $user = Auth::user();
        if ($user->cannot('viewAny', Event::class)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $start_time = $request->query('start_time');
        $end_time = $request->query('end_time');

        if (! empty($start_time) && ! empty($end_time)) {
            if ($start_time > $end_time) {
                return response()->json(['error' => 'Invalid date range'], 400);
            }

            $locations = $user->events()
                ->whereBetween('start_time', [$start_time, $end_time])
                ->orWhereBetween('end_time', [$start_time, $end_time])
                ->orWhere(function ($query) use ($start_time, $end_time) {
                    $query->where('start_time', '<', $start_time)->where('end_time', '>', $end_time);
                })
                ->select('location')
                ->distinct()
                ->pluck('location');
        } else {
            $locations = $user->events()
                ->select('location')
                ->distinct()
                ->pluck('location');
        }

        $event_data = [];
        foreach ($locations as $location) {
            $event_data[] = [
                'location' => $location,
                'weather_conditions' => $this->weatherService->getWeatherData($location, $start_time),
            ];
        }

        return response()->json($event_data, 200);
    }
}
