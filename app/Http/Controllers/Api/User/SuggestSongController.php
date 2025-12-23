<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\SuggestSong;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            'email' => 'required|email|max:255',
        ]);

        // Generate unique slug from title
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        
        while (SuggestSong::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;
        $validated['status'] = 1; // Default to pending

        $suggestSong = SuggestSong::create($validated);

        return response()->json([
            'message' => 'Song suggestion submitted successfully',
            'data' => $suggestSong
        ], 201);
    }
}
