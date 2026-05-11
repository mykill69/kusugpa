<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function __construct()
    {
        // Ensure only Administrator and super_admin can access this controller
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!in_array($user->role, ['Administrator', 'super_admin'])) {
                return redirect()->route('dashboard')
                    ->with('error', 'You do not have permission to access User Management.');
            }
            return $next($request);
        });
    }

    public function userManagement()
    {
        $users = User::with('permissions')->orderBy('created_at', 'desc')->get();
        
        return view('user-management.index', [
            'users' => $users,
            'allPermissions' => Permission::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['nullable', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'in:Administrator,super_admin,manager,loan_officer,User,Viewer'],
        ]);

        $currentUser = auth()->user();

        // Only Administrator can create Administrator accounts
        if ($request->role === 'Administrator' && $currentUser->role !== 'Administrator') {
            return response()->json([
                'message' => 'Only an Administrator can create Administrator accounts.'
            ], 403);
        }

        // Only Administrator or super_admin can create super_admin accounts
        if ($request->role === 'super_admin' && !in_array($currentUser->role, ['Administrator', 'super_admin'])) {
            return response()->json([
                'message' => 'Only an Administrator or Super Admin can create Super Admin accounts.'
            ], 403);
        }

        $user = User::create([
            'fname' => $request->fname,
            'mname' => $request->mname ?? null,
            'lname' => $request->lname ?? '',
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->load('permissions')
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
        'fname' => ['required', 'string', 'max:255'],
        'lname' => ['nullable', 'string', 'max:255'],
        'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
        'role' => ['required', 'in:Administrator,super_admin,manager,loan_officer,User,Viewer'],
    ]);

        $currentUser = auth()->user();
        $isOwnAccount = $user->id === $currentUser->id;

        // Administrator can edit their own account (but cannot change their own role)
        if ($isOwnAccount && $currentUser->role === 'Administrator') {
            if ($request->role !== 'Administrator') {
                return response()->json([
                    'message' => 'You cannot change your own role from Administrator.'
                ], 403);
            }
            
            // Allow Administrator to update their own info (name, username, password)
            $updateData = [
                'fname' => $request->fname,
                'mname' => $request->mname ?? $user->mname,
                'lname' => $request->lname ?? '',
                'username' => $request->username,
                'role' => $request->role, // Must remain Administrator
            ];

            if ($request->filled('password')) {
                $request->validate(['password' => [Password::defaults()]]);
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'message' => 'Your account has been updated successfully',
                'user' => $user->fresh()->load('permissions')
            ]);
        }

        // Super admin editing their own account
        if ($isOwnAccount && $currentUser->role === 'super_admin') {
            if ($request->role !== 'super_admin') {
                return response()->json([
                    'message' => 'You cannot change your own role.'
                ], 403);
            }
        }

        // Other users cannot edit their own account
        if ($isOwnAccount && !in_array($currentUser->role, ['Administrator', 'super_admin'])) {
            return response()->json([
                'message' => 'You cannot edit your own account.'
            ], 403);
        }

        // Cannot modify Administrator accounts unless you're an Administrator
        if ($user->role === 'Administrator' && $currentUser->role !== 'Administrator') {
            return response()->json([
                'message' => 'Only an Administrator can modify Administrator accounts.'
            ], 403);
        }

        // Only Administrator can promote users to Administrator
        if ($request->role === 'Administrator' && 
            $user->role !== 'Administrator' && 
            $currentUser->role !== 'Administrator') {
            return response()->json([
                'message' => 'Only an Administrator can promote users to Administrator.'
            ], 403);
        }

        // Super admin cannot change Administrator's role
        if ($user->role === 'Administrator' && $currentUser->role === 'super_admin') {
            return response()->json([
                'message' => 'Super admin cannot modify Administrator accounts.'
            ], 403);
        }

        // Super admin cannot demote another super_admin (only Administrator can)
        if ($user->role === 'super_admin' && 
            $currentUser->role === 'super_admin' && 
            !in_array($request->role, ['Administrator', 'super_admin'])) {
            return response()->json([
                'message' => 'You cannot demote other super_admin accounts. Only Administrator can do this.'
            ], 403);
        }

        $updateData = [
            'fname' => $request->fname,
            'mname' => $request->mname ?? $user->mname,
            'lname' => $request->lname ?? '',
            'username' => $request->username,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => [Password::defaults()]]);
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->fresh()->load('permissions')
        ]);
    }

    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        // Cannot delete yourself
        if ($user->id === $currentUser->id) {
            return response()->json(['message' => 'Cannot delete your own account'], 403);
        }

        // Cannot delete Administrator accounts unless you're an Administrator
        if ($user->role === 'Administrator' && $currentUser->role !== 'Administrator') {
            return response()->json([
                'message' => 'Only an Administrator can delete Administrator accounts.'
            ], 403);
        }

        // Super admin cannot delete Administrator accounts
        if ($user->role === 'Administrator' && $currentUser->role === 'super_admin') {
            return response()->json([
                'message' => 'Super admin cannot delete Administrator accounts.'
            ], 403);
        }

        // Super admin can delete Users and Viewers, but not other super_admins
        if ($user->role === 'super_admin' && $currentUser->role === 'super_admin') {
            return response()->json([
                'message' => 'Super admin cannot delete other super_admin accounts. Only Administrator can do this.'
            ], 403);
        }

        $user->permissions()->detach();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function assignPermissions(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'permissions' => ['required', 'array'],
            'permissions.*.slug' => ['required', 'string', 'exists:permissions,slug'],
            'permissions.*.granted' => ['required', 'boolean'],
        ]);

        $currentUser = auth()->user();
        $user = User::findOrFail($request->user_id);

        // Don't modify Administrator or super_admin permissions
        if ($user->role === 'Administrator' || $user->role === 'super_admin') {
            return response()->json([
                'message' => $user->role . ' has all permissions by default'
            ], 403);
        }

        // Only Administrator and super_admin can manage permissions
        if (!in_array($currentUser->role, ['Administrator', 'super_admin'])) {
            return response()->json([
                'message' => 'You do not have permission to manage permissions.'
            ], 403);
        }

        $grantedSlugs = [];
        foreach ($request->permissions as $permission) {
            if ($permission['granted']) {
                $grantedSlugs[] = $permission['slug'];
            }
        }
        

        $user->syncPermissions($grantedSlugs);

        return response()->json([
            'message' => 'Permissions updated successfully',
            'permissions' => $user->fresh()->permissions
        ]);
    }

    public function permissionsList()
    {
        $permissions = Permission::all()->map(function($p) {
            return [
                'id' => $p->id,
                'slug' => $p->slug,
                'label' => $p->name,
                'description' => $p->description,
            ];
        });
        
        return response()->json($permissions);
    }
}
