<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return response()->json([
            'status' => 200,
            'data' => $permissions,
            'message' => 'Permissions retrieved successfully.',
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 201,
            'data' => $permission,
            'message' => 'Permission created successfully.',
        ], 201); // Created
    }

    public function show(Permission $permission)
    {
        return response()->json([
            'status' => 200,
            'data' => $permission,
            'message' => 'Permission retrieved successfully.',
        ], 200);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 200,
            'data' => $permission,
            'message' => 'Permission updated successfully.',
        ], 200);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json([
            'status' => 204, // No Content
            'message' => 'Permission deleted successfully.',
        ], 204);
    }
}
