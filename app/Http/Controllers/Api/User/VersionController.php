<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VersionController extends Controller
{
    /**
     * Check if app needs force update
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkForceUpdate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'version_code' => 'required|integer',
                'platform' => 'required|in:android,ios'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => $e->validator->errors()->first()
            ], 422);
        }

        $currentVersionCode = $request->input('version_code');
        $platform = $request->input('platform');

        // Get the latest version info from database
        $latestVersion = AppVersion::getLatestVersion($platform);
        $forceUpdateVersion = AppVersion::getMinimumForceUpdateVersion($platform);

        if (!$latestVersion || !$forceUpdateVersion) {
            return response()->json([
                'needs_update' => false,
                'current_version_code' => $currentVersionCode,
                'message' => 'Version information not available'
            ]);
        }

        // Check if update is required
        $needsUpdate = $currentVersionCode < $forceUpdateVersion->version_code;

        return response()->json([
            'needs_update' => $needsUpdate,
            'current_version_code' => $currentVersionCode,
            'minimum_version_code' => $forceUpdateVersion->version_code,
            'latest_version_code' => $latestVersion->version_code,
            'latest_version_name' => $latestVersion->version_name,
            'update_url' => $latestVersion->update_url,
            'release_notes' => $latestVersion->release_notes,
            'message' => $needsUpdate
                ? 'A new version is available. Please update to continue using the app.'
                : 'Your app is up to date.'
        ]);
    }
}
