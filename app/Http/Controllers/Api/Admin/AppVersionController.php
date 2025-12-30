<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppVersionController extends Controller
{
    public function index(Request $request)
    {
        $appVersions = AppVersion::query()
            ->when($request->platform, function ($query, $platform) {
                $query->where('platform', $platform);
            })
            ->orderBy('platform')
            ->orderByDesc('version_code')
            ->paginate(10);

        return response()->json($appVersions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string|in:android,ios',
            'version_code' => 'required|integer|min:1',
            'version_name' => 'required|string|max:50',
            'update_url' => 'required|string|url|max:255',
            'release_notes' => 'required|string',
            'is_force_update' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $this->authorize('create', AppVersion::class);

        $appVersion = AppVersion::create($request->all());

        return response()->json($appVersion, 201);
    }

    public function show(AppVersion $appVersion)
    {
        return response()->json($appVersion);
    }

    public function update(Request $request, AppVersion $appVersion)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'sometimes|required|string|in:android,ios',
            'version_code' => 'sometimes|required|integer|min:1',
            'version_name' => 'sometimes|required|string|max:50',
            'update_url' => 'sometimes|required|string|url|max:255',
            'release_notes' => 'sometimes|required|string',
            'is_force_update' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $this->authorize('update', $appVersion);

        $appVersion->update($request->all());

        return response()->json($appVersion);
    }

    public function destroy(AppVersion $appVersion)
    {
        $this->authorize('delete', $appVersion);

        $appVersion->delete();

        return response()->json(null, 204);
    }
}
