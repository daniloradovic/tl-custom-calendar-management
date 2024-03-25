@extends('emails.layout')

@section('content')

<div class="text-center">
    <h2 class="text-2xl font-bold">Event Invitation</h2>
    <p class="mt-4">Hey there,</p>
    <p class="mt-2">You have been invited to the event <strong>{{ $event->title }}</strong> by <strong>{{ $event->user->name }}</strong>.</p>
    <p class="mt-2">Event Details:</p>
    <ul class="list-disc ml-6 mt-2">
        <li><strong>Name:</strong> {{ $event->title }}</li>
        <li><strong>Date:</strong> {{ $event->start_time }}</li>
        <li><strong>Location:</strong> {{ $event->location }}</li>
        {{-- <li><strong>Weather Forecast: {{ $event->weather->forecast }}</strong></li>
        <li><strong>Temperature: {{  $event->weather->temperature }}°C</strong></li>
        <li><strong>Percipitation: {{ $event->weather->percipitation }}%</strong></li> --}}
    </ul>                        
</div>

@endsection