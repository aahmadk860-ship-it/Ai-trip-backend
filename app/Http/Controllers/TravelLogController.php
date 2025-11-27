<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TravelLog;

class TravelLogController extends Controller
{
    public function index(Request $request)
{
    // Optional: Filter logs by username if provided in the query string
    $userName = $request->query('user_name');

    $query = TravelLog::query();

    if ($userName) {
        $query->where('user_name', $userName);
    }

    // Paginate results (10 per page by default)
    $travelLogs = $query->orderBy('created_at', 'desc')->get();

    // Return structured JSON response
    return response()->json([
        'success' => true,
        'message' => $userName 
            ? "Travel logs for user: {$userName}" 
            : "All travel logs retrieved successfully.",
        'data' => $travelLogs
    ]);
}

    public function store(Request $request)
{
    $validated = $request->validate([
        'user_name' => 'required|string|max:255',
        'action'    => 'required|string|max:255',
        'location'  => 'required|string|max:255',
        'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'notes'     => 'nullable|string',
    ]);

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('travel_logs', 'public');
        $validated['image'] = '/storage/' . $imagePath; // public URL
    }

    $travelLog = TravelLog::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Travel log created successfully.',
        'data' => [
            'id'        => $travelLog->id,
            'user_name' => $travelLog->user_name,
            'action'    => $travelLog->action,
            'location'  => $travelLog->location,
            'image'     => $travelLog->image ?? null,
            'notes'     => $travelLog->notes ?? null,
            'created_at'=> $travelLog->created_at->toDateTimeString(),
        ],
    ], 201);
}

}

