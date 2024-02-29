<?php

namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Events\formCreated;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json([
            'status' => 200,
            'data' => $roles,
            'message' => 'Roles retrieved successfully.',
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);
broadcast(new formCreated('New Role created successfully.'));
        return response()->json([
            'status' => 201,
            'data' => $role,
            'message' => 'Role created successfully.',
        ], 201);
    }

    public function show(Role $role)
    {
        return response()->json([
            'status' => 200,
            'data' => $role->load('permissions'),
            'message' => 'Role retrieved successfully.',
        ], 200);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 200,
            'data' => $role,
            'message' => 'Role updated successfully.',
        ], 200);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json([
            'status' => 204,
            'message' => 'Role deleted successfully.',
        ], 204);
    }

    public function addPermission(Request $request, Role $role)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $permission = Permission::findOrFail($request->permission_id);
        if (!$role->permissions->contains($permission)) {
            $role->permissions()->attach($permission);
            return response()->json([
                'status' => 200,
                'message' => 'Permission added successfully',
            ], 200);
        }

        return response()->json([
            'status' => 400,
            'message' => 'This role already has the specified permission',
        ], 400);
    }

    public function removePermission(Request $request, Role $role)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $permission = Permission::findOrFail($request->permission_id);
        if ($role->permissions->contains($permission)) {
            $role->permissions()->detach($permission);
            return response()->json([
                'status' => 200,
                'message' => 'Permission removed successfully',
            ], 200);
        }

        return response()->json([
            'status' => 400,
            'message' => 'This role does not have the specified permission',
        ], 400);
    }
}
