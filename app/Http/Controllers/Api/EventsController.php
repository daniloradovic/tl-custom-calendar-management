<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventsController extends Controller
{
    public function store(StoreEventRequest $request)
    {
        $event = Event::create($request->validated());

        return response()->json($event, 201);
    }

    public function show(Event $event)
    {
        // Check if user is authorized to view the event
        if (! Auth::user()->can('view', $event)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        // Check if user is authorized to delete the event
        if (! Auth::user()->can('delete', $event)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Soft delete the event
        $event->delete();

        return response()->json(null, 204);
    }

    public function update(StoreEventRequest $request, Event $event)
    {
        // Check if user is authorized to update the event
        if (! Auth::user()->can('update', $event)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $event->update($request->validated());

        return response()->json($event);
    }

    public function locations(Request $request)
    {
        
    }
}
