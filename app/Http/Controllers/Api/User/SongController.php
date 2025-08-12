<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\Category;
use App\Models\SongLanguage;
use App\Models\Style;
use Illuminate\Http\Request;

class SongController extends Controller
{
    /**
     * Display a listing of songs for users.
     */
    public function index(Request $request)
    {
        $songs = Song::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })->orWhere(function ($q) use ($search) {
                    $q->where('lyrics', 'like', "%{$search}%");
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
            ->with(['style', 'categories', 'songLanguages']);

        if ($request->has('limit')) {
            $songs = $songs->paginate($request->limit);
        } else {
            $songs = [
                'data' => $songs->get()
            ];
        }

        return response()->json($songs);
    }

    /**
     * Display the specified song details.
     */
    public function show($slug)
    {
        $song = Song::where('slug', $slug)->firstOrFail();
        $song->load('style', 'categories', 'songLanguages');

        return response()->json([
            'id' => $song->id,
            'code' => $song->code,
            'title' => $song->title,
            'slug' => $song->slug,
            'youtube' => $song->youtube,
            'description' => $song->description,
            'song_writer' => $song->song_writer,
            'style' => $song->style,
            'categories' => $song->categories,
            'song_languages' => $song->songLanguages,
            'lyrics' => $song->lyrics,
            'music_notes' => $song->music_notes,
            'created_at' => $song->created_at,
            'updated_at' => $song->updated_at,
        ]);
    }

    /**
     * Display a listing of all categories.
     */
    public function categories()
    {
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);
        return response()->json($categories);
    }

    /**
     * Display a listing of all categories and styles for search filters.
     */
    public function searchFilters()
    {
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);
        $styles = Style::orderBy('name')->get(['id', 'name']);
        $songLanguages = SongLanguage::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'categories' => $categories,
            'styles' => $styles,
            'song_languages' => $songLanguages
        ]);
    }
}
