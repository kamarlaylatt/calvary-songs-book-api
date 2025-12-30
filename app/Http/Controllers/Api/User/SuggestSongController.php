<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\SuggestSong;
use Illuminate\Http\Request;

class SuggestSongController extends Controller
{
    /**
     * Store a newly suggested song from mobile app.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
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
            // Categories can be provided as an array of IDs.
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            // Song languages can be provided as an array of IDs.
            'song_language_ids' => 'nullable|array',
            'song_language_ids.*' => 'integer|exists:song_languages,id',
        ]);

        $validated['status'] = SuggestSong::STATUS_PENDING; // Default to pending

        // Do not persist category_ids and song_language_ids directly on model
        $categoryIds = $request->input('category_ids');
        $songLanguageIds = $request->input('song_language_ids');
        unset($validated['category_ids'], $validated['song_language_ids']);

        $suggestSong = SuggestSong::create($validated);

        // Allow single ID or array; normalize and sync if provided
        if (! is_null($categoryIds)) {
            if (is_numeric($categoryIds)) {
                $categoryIds = [(int) $categoryIds];
            }
            if (is_array($categoryIds) && ! empty($categoryIds)) {
                $suggestSong->categories()->sync($categoryIds);
            }
        }

        if (! is_null($songLanguageIds)) {
            if (is_numeric($songLanguageIds)) {
                $songLanguageIds = [(int) $songLanguageIds];
            }
            if (is_array($songLanguageIds) && ! empty($songLanguageIds)) {
                $suggestSong->songLanguages()->sync($songLanguageIds);
            }
        }

        return response()->json([
            'message' => 'Song suggestion submitted successfully',
            'data' => $suggestSong->load(['categories', 'songLanguages']),
        ], 201);
    }
}
