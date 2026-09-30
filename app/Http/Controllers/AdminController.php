<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_doctors' => User::where('role', 'doctor')->count(),
            'total_receptions' => User::where('role', 'reception')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_users' => User::count(),
        ];

        $recentUsers = User::whereIn('role', ['doctor', 'admin'])
            ->with('doctorProfile')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }

    /**
     * Display list of doctors.
     */
    public function doctors()
    {
        $doctors = User::where('role', 'doctor')
            ->with('doctorProfile')
            ->latest()
            ->paginate(15);

        return view('admin.doctors', compact('doctors'));
    }

    /**
     * Show form to create a new doctor.
     */
    public function createDoctor()
    {
        return view('admin.create-doctor');
    }

    /**
     * Store a new doctor.
     */
    public function storeDoctor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        // Create user
        $doctor = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'doctor',
            'email_verified_at' => now(),
        ]);

        // Create doctor profile
        DoctorProfile::create([
            'user_id' => $doctor->id,
            'specialization' => $request->specialization,
            'license_number' => $request->license_number,
            'phone' => $request->phone,
        ]);

        return redirect()
            ->route('admin.doctors')
            ->with('success', 'Doctor created successfully!');
    }

    /**
     * Show form to edit a doctor.
     */
    public function editDoctor(User $doctor)
    {
        // Ensure the user is a doctor
        if ($doctor->role !== 'doctor') {
            return redirect()->route('admin.doctors')->with('error', 'Invalid doctor!');
        }

        return view('admin.edit-doctor', compact('doctor'));
    }

    /**
     * Update a doctor.
     */
    public function updateDoctor(Request $request, User $doctor)
    {
        // Ensure the user is a doctor
        if ($doctor->role !== 'doctor') {
            return redirect()->route('admin.doctors')->with('error', 'Invalid doctor!');
        }

        // Validate input
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->id,
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];

        // Password is optional on update
        if ($request->filled('password')) {
            $rules['password'] = 'min:8|confirmed';
        }

        $request->validate($rules);

        // Update user info
        $doctor->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $doctor->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Update doctor profile
        $doctor->doctorProfile()->updateOrCreate(
            ['user_id' => $doctor->id],
            [
                'specialization' => $request->specialization,
                'license_number' => $request->license_number,
                'phone' => $request->phone,
            ]
        );

        return redirect()
            ->route('admin.doctors')
            ->with('success', 'Doctor updated successfully!');
    }

    /**
     * Delete a user.
     */
    public function deleteUser(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        // Prevent deleting the last admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last admin account!');
        }

        // Admin can only delete doctors and other admins, NOT reception users
        if ($user->role === 'reception') {
            return back()->with('error', 'Admins cannot delete reception users. Contact the doctor who created them.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }
}
