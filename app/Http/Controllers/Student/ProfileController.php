<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('student.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information (name, email, phone, parent_phone + optional avatar).
     */
    public function update(Request $request)
    {
        $user    = Auth::user();
        $student = $user->student;

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'phone'        => 'nullable|string|max:30',
            'parent_phone' => 'nullable|string|max:30',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Update auth user
        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        // Update student record
        if ($student) {
            $studentData = [
                'phone'        => $validated['phone'] ?? $student->phone,
                'parent_phone' => $validated['parent_phone'] ?? $student->parent_phone,
            ];

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete the old picture if it exists
                if ($student->profile_picture) {
                    Storage::disk('public')->delete($student->profile_picture);
                }
                // Store new picture under profile_pictures/
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $studentData['profile_picture'] = $path;
            }

            $student->update($studentData);
        }

        return redirect()->route('student.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password only.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        $user           = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('student.profile')
            ->with('success', 'Password updated successfully!');
    }
}
