<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\SuggestSong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SuggestSongController extends Controller
{
    /**
     * Display a listing of the resource with optional filters.
     */
    public function index(Request $request)
    {
        $suggestions = SuggestSong::query()
            ->when($request->id, function ($query, $id) {
                $query->where('id', $id);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('lyrics', 'like', "%{$search}%")
                        ->orWhere('code', $search);
                });
            })
            ->when($request->style_id, function ($query, $styleId) {
                $query->where('style_id', $styleId);
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15);

        return response()->json($suggestions);
    }

    /**
     * Display the specified resource.
     */
    public function show(SuggestSong $suggestSong)
    {
        return response()->json($suggestSong);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuggestSong $suggestSong)
    {
        $validated = $request->validate([
            'code' => 'sometimes|required|integer',
            'title' => 'sometimes|required|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'song_writer' => 'nullable|string|max:255',
            'style_id' => 'nullable|exists:styles,id',
            'key' => 'nullable|string|max:255',
            'lyrics' => 'sometimes|required|string',
            'music_notes' => 'nullable|string',
            'popular_rating' => 'nullable|integer|min:0|max:5',
            'email' => 'nullable|email|max:255',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title'], $suggestSong->id);
        }

        $suggestSong->update($validated);

        return response()->json($suggestSong);
    }

    /**
     * Approve the suggestion and create a song entry.
     */
    public function approve(SuggestSong $suggestSong)
    {
        if ($suggestSong->status === 2) {
            return response()->json(['message' => 'Suggestion already approved'], 422);
        }

        $admin = auth('admin')->user();

        $code = $suggestSong->code ?? (Song::max('code') + 1);
        if (Song::where('code', $code)->exists()) {
            $code = Song::max('code') + 1;
        }

        $songData = [
            'code' => $code,
            'title' => $suggestSong->title,
            'slug' => Str::slug($suggestSong->title) . '-' . $code,
            'youtube' => $suggestSong->youtube,
            'description' => $suggestSong->description,
            'song_writer' => $suggestSong->song_writer,
            'style_id' => $suggestSong->style_id,
            'lyrics' => $suggestSong->lyrics,
            'music_notes' => $suggestSong->music_notes,
            'popular_rating' => $suggestSong->popular_rating,
        ];

        $song = $admin
            ? $admin->songs()->create($songData)
            : Song::create($songData);

        $suggestSong->update(['status' => 2]);

        Cache::flush();

        return response()->json([
            'message' => 'Suggestion approved and song created',
            'suggestion' => $suggestSong,
            'song' => $song->load(['style', 'categories', 'songLanguages']),
        ]);
    }

    /**
     * Cancel a suggestion.
     */
    public function cancel(SuggestSong $suggestSong)
    {
        $suggestSong->update(['status' => 0]);

        return response()->json([
            'message' => 'Suggestion cancelled',
            'suggestion' => $suggestSong,
        ]);
    }

    private function generateUniqueSlug(string $title, int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (
            SuggestSong::where('slug', $slug)
                ->when($ignoreId, function ($query, $ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
