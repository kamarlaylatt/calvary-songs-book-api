<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Style;
use Illuminate\Http\Request;

class StyleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Style::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:styles,name',
        ]);

        $this->authorize('create', Style::class);

        $style = Style::create($validated);

        return response()->json($style, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Style $style)
    {
        return response()->json($style);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Style $style)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:styles,name,'.$style->id,
        ]);

        $this->authorize('update', $style);

        $style->update($validated);

        return response()->json($style);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Style $style)
    {
        $this->authorize('delete', $style);

        $style->delete();

        return response()->json(null, 204);
    }
}
