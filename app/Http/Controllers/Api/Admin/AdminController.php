<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::with('roles')->latest()->paginate(10);
        return response()->json($admins);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $this->authorize('create', Admin::class);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ]);

        if ($request->has('roles')) {
            $admin->roles()->sync($request->roles);
        }

        return response()->json($admin->load('roles'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        return response()->json($admin->load('roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('admins')->ignore($admin->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'sometimes|required|in:active,inactive',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $this->authorize('update', $admin);

        if ($request->has('name')) {
            $admin->name = $request->name;
        }

        if ($request->has('email')) {
            $admin->email = $request->email;
        }

        if ($request->has('password') && $request->password) {
            $admin->password = Hash::make($request->password);
        }

        if ($request->has('status')) {
            $admin->status = $request->status;
        }

        $admin->save();

        if ($request->has('roles')) {
            $admin->roles()->sync($request->roles);
        }

        return response()->json($admin->load('roles'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $this->authorize('delete', $admin);

        $admin->delete();
        return response()->json(null, 204);
    }
}
