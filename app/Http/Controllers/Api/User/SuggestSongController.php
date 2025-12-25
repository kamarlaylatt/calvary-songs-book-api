<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\SuggestSong;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuggestSongController extends Controller
{
    /**
     * Store a newly suggested song from mobile app.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|integer',
            'title' => 'required|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'song_writer' => 'nullable|string|max:255',
            'style_id' => 'nullable|exists:styles,id',
            'key' => 'nullable|string|max:255',
            'lyrics' => 'required|string',
            'music_notes' => 'nullable|string',
            'popular_rating' => 'nullable|integer|min:0|max:5',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['slug'] = SuggestSong::generateUniqueSlug($validated['title']);
        $validated['status'] = SuggestSong::STATUS_PENDING; // Default to pending

        $suggestSong = SuggestSong::create($validated);

        return response()->json([
            'message' => 'Song suggestion submitted successfully',
            'data' => $suggestSong
        ], 201);
    }
}
