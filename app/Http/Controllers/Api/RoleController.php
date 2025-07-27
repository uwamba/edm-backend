<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // List all roles (for the frontend dropdown)
    public function index()
    {
        // Return all roles with just id and name
        return response()->json(Role::select('id', 'name')->get());
    }

    // Create a new role (from the modal)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web', // adjust guard if needed
        ]);

        return response()->json($role, 201);
    }
}
