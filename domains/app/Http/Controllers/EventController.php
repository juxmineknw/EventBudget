<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Routing\Controller;

class EventController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Event::all()
        ]);
    }

    public function show($id)
    {
        $event = Event::with(['details', 'team'])->findOrFail($id);

        return response()->json($event);
    }
}
