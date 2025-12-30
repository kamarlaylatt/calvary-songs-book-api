<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SongLanguage;
use Illuminate\Http\Request;

class SongLanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(SongLanguage::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:song_languages,name',
        ]);

        $this->authorize('create', SongLanguage::class);

        $songLanguage = SongLanguage::create($validated);

        return response()->json($songLanguage, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SongLanguage $songLanguage)
    {
        return response()->json($songLanguage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SongLanguage $songLanguage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:song_languages,name,'.$songLanguage->id,
        ]);

        $this->authorize('update', $songLanguage);

        $songLanguage->update($validated);

        return response()->json($songLanguage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SongLanguage $songLanguage)
    {
        $this->authorize('delete', $songLanguage);

        $songLanguage->delete();

        return response()->json(null, 204);
    }
}
