<?php

namespace App\Http\Controllers\Api;

use App\Contracts\WeatherServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
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
     * @param  Request  $request  The request object.
     *
     * @LRDparam  start_time (YYYY-MM-DD).
     * @LRDparam  end_time (YYYY-MM-DD).
     * @LRDparam  page integer.
     *
     * @return \Illuminate\Http\JsonResponse
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
                ->orderBy('start_time')
                ->with('invitees')
                ->paginate(5);
        } else {
            $events = $user->events()
                ->with('invitees')
                ->orderBy('start_time')
                ->paginate(5);
        }

        return response()->json([
            'events' => $events->items(),
            'total' => $events->total(),
            'per_page' => $events->perPage(),
            'current_page' => $events->currentPage(),
        ]);
    }

    /**
     * Display the specified event.
     *
     * @param  int  $id  The event ID.
     * @return \Illuminate\Http\JsonResponse
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

        $weather_data = $this->weatherService->getWeatherData($event->location, $event->start_time);

        $event->weather = $weather_data;
        $event->invitees = $invitees;

        // send email to invitees in the background using a job
        CreateInvitees::dispatch($invitees, $event);

        return response()->json($event, 201);
    }

    /**
     * Delete an event.
     *
     * @param  int  $id  The event ID.
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $event = Event::find($id);
        if (! $event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        $user = Auth::user();
        if (! $user->can('delete', $event)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $event->delete();

        return response()->json(null, 204);
    }

    /**
     * Update an event.
     *
     * @param  UpdateEventRequest  $request  The request object containing the event data.
     * @param  Event  $event  The event to be updated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $user = Auth::user();
        if (! $user->can('update', $event)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $event->update($request->validated());

        UpdateInvitees::dispatch($event, $request->invitees);

        return response()->json($event, 200);
    }

    /**
     * Retrieve the locations for the events.
     *
     * @LRDparam  start_time (YYYY-MM-DD).
     * @LRDparam  end_time (YYYY-MM-DD).
     *
     * @param  Request  $request  The request object.
     * @return \Illuminate\Http\JsonResponse
     */
    public function locations(Request $request)
    {
        $user = Auth::user();
        if ($user->cannot('viewAny', Event::class)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $start_time = $request->has('start_time') ? $request->query('start_time') : null;
        $end_time = $request->has('end_time') ? $request->query('end_time') : null;

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
                ->select('start_time', 'end_time', 'location')
                ->orderBy('start_time')
                ->distinct()
                ->get('location', 'start_time', 'end_time');
        } else {
            $locations = $user->events()
                ->orderBy('start_time')
                ->select('start_time', 'end_time', 'location')
                ->distinct()
                ->get('location', 'start_time', 'end_time');
        }

        $event_data = [];
        foreach ($locations as $location) {

            $event_data[] = [
                'location' => $location->location,
                'start_time' => $location->start_time,
                'end_time' => $location->end_time,
                'weather_conditions' => $this->weatherService->getWeatherData($location->location, $location->start_time),
            ];
        }

        return response()->json($event_data, 200);
    }
}
