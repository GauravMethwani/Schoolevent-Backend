<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    protected $departments = [
        'Science',
        'Mathematics',
        'English',
        'Sports',
        'Cultural',
        'Art & Craft',
        'Computer Science',
        'Social Studies',
        'Commerce',
        'General'
    ];

    protected $types = [
        'Seminar',
        'Workshop',
        'Celebration',
        'Exhibition',
        'Orientation',
        'Sports',
        'Cultural',
        'Tour'
    ];

    //Get All Events (with search, sort, pagination)
    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('type', 'like', "%$search%")
                  ->orWhere('department', 'like', "%$search%");
            });
        }

        if ($request->filled('sort_by') && in_array($request->sort_by, ['department', 'type', 'date'])) {
            $query->orderBy($request->sort_by, $request->get('sort_order', 'asc'));
        }

        return response()->json($query->paginate($request->get('per_page', 10)));
    }

    // Get Single Event by ID
    public function show($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }
        return response()->json($event);
    }

    //Create New Event
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string',
            'organizer' => 'required|string',
            'department' => 'required|in:' . implode(',', $this->departments),
            'type' => 'required|in:' . implode(',', $this->types),
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event = Event::create($data);

        return response()->json($event, 201);
    }

    //Update Existing Event
    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string',
            'date' => 'sometimes|required|date',
            'location' => 'sometimes|required|string',
            'organizer' => 'sometimes|required|string',
            'department' => 'sometimes|required|in:' . implode(',', $this->departments),
            'type' => 'sometimes|required|in:' . implode(',', $this->types),
            'description' => 'sometimes|required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        return response()->json($event);
    }

    //Delete Event
    public function destroy($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event->delete();
        return response()->json(['message' => 'Event deleted successfully']);
    }
}
