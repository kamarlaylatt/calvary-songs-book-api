<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SuggestSongApproved;
use App\Models\Song;
use App\Models\SuggestSong;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SuggestSongController extends Controller
{
    /**
     * Display a listing of the resource with optional filters.
     */
    public function index(Request $request)
    {
        $suggestions = SuggestSong::query()
            ->with(['categories', 'songLanguages', 'style'])
            ->when($request->id, function ($query, $id) {
                $query->where('id', $id);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('lyrics', 'like', "%{$search}%");
                });
            })
            ->when($request->style_id, function ($query, $styleId) {
                $query->where('style_id', $styleId);
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            })
            ->when($request->song_language_id, function ($query, $songLanguageId) {
                $query->whereHas('songLanguages', function ($q) use ($songLanguageId) {
                    $q->where('song_languages.id', $songLanguageId);
                });
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
        return response()->json($suggestSong->load(['categories', 'songLanguages', 'style']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuggestSong $suggestSong)
    {
        $validated = $request->validate([
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
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'song_language_ids' => 'nullable|array',
            'song_language_ids.*' => 'integer|exists:song_languages,id',
        ]);

        // Do not persist category_ids and song_language_ids directly on model
        $categoryIds = $request->input('category_ids');
        $songLanguageIds = $request->input('song_language_ids');
        unset($validated['category_ids'], $validated['song_language_ids']);

        $suggestSong->update($validated);

        // Allow single ID or array; normalize and sync if provided
        if (!is_null($categoryIds)) {
            if (is_numeric($categoryIds)) {
                $categoryIds = [(int) $categoryIds];
            }
            if (is_array($categoryIds) && !empty($categoryIds)) {
                $suggestSong->categories()->sync($categoryIds);
            }
        }

        if (!is_null($songLanguageIds)) {
            if (is_numeric($songLanguageIds)) {
                $songLanguageIds = [(int) $songLanguageIds];
            }
            if (is_array($songLanguageIds) && !empty($songLanguageIds)) {
                $suggestSong->songLanguages()->sync($songLanguageIds);
            }
        }

        return response()->json($suggestSong->load(['categories', 'songLanguages']));
    }

    /**
     * Approve the suggestion and create a song entry.
     */
    public function approve(SuggestSong $suggestSong)
    {
        if ($suggestSong->status === SuggestSong::STATUS_APPROVED) {
            return response()->json(['message' => 'Suggestion already approved'], 422);
        }

        $admin = auth('admin')->user();
        $song = DB::transaction(function () use ($suggestSong, $admin) {

            $code = Song::nextCode(true);

            $songData = [
                'code' => $code,
                'title' => $suggestSong->title,
                'slug' => Song::generateSlug($suggestSong->title, $code),
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

            // Copy categories and songLanguages from suggestion to created song
            $categoryIds = $suggestSong->categories()->pluck('categories.id')->all();
            if (!empty($categoryIds)) {
                $song->categories()->sync($categoryIds);
            }

            $songLanguageIds = $suggestSong->songLanguages()->pluck('song_languages.id')->all();
            if (!empty($songLanguageIds)) {
                $song->songLanguages()->sync($songLanguageIds);
            }

            $suggestSong->update(['status' => SuggestSong::STATUS_APPROVED]);

            return $song;
        });

        Cache::flush();

        if (!empty($suggestSong->email)) {
            Mail::to($suggestSong->email)->send(new SuggestSongApproved($suggestSong, $song));
        }

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
        if ($suggestSong->status !== SuggestSong::STATUS_PENDING) {
            return response()->json(['message' => 'Only pending suggestions can be cancelled'], 422);
        }

        $suggestSong->update(['status' => SuggestSong::STATUS_CANCELLED]);

        return response()->json([
            'message' => 'Suggestion cancelled',
            'suggestion' => $suggestSong,
        ]);
    }
}
