<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Bed;
use App\Models\User;
use App\Models\Ward;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class SuperAdminController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if the current user is a superadmin
     */
    private function checkSuperAdmin()
    {
        if (!Auth::check()) {
            abort(403, 'User not authenticated.');
        }

        $user = Auth::user();
        \Log::info('Checking superadmin access for user:', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'raw_role' => $user->getAttributes()['role'] ?? 'not set'
        ]);

        // If role is null, try to set it based on the user's name or email
        if ($user->role === null) {
            if (strtolower($user->name) === 'superadmin' ||
                (strpos(strtolower($user->email), 'admin') !== false)) {
                $user->role = 'superadmin';
                $user->save();
                \Log::info('Updated user role to superadmin based on name/email');
            } else {
                abort(403, 'User role not set. Please contact administrator.');
            }
        }

        if ($user->role !== 'superadmin') {
            abort(403, 'Unauthorized action. Superadmin access required. Current role: ' . $user->role);
        }
    }

    /**
     * Show the super admin dashboard.
     */
    public function dashboard()
    {
        $this->checkSuperAdmin();

        $recentActivities = Activity::latest()->take(10)->get();
        $totalUsers = User::count();
        $totalWards = Ward::count();
        $totalBeds = Bed::count();

        return view('super-admin.dashboard', compact(
            'recentActivities',
            'totalUsers',
            'totalWards',
            'totalBeds'
        ));
    }

    /**
     * Show the ward management page.
     */
    public function wardManagement()
    {
        $this->checkSuperAdmin();

        $wards = Ward::with(['rooms.beds'])->get();
        return view('super-admin.ward-management', compact('wards'));
    }

    /**
     * Add new ward.
     */
    public function addWard(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'ward_name' => 'required|string|max:255|unique:wards,ward_name',
        ]);

        Ward::create([
            'ward_name' => $request->ward_name,
        ]);

        return redirect()->route('super-admin.ward-management')
            ->with('success', 'Ward added successfully.');
    }

    /**
     * Add new room to a ward.
     */
    public function addRoom(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'ward_id' => 'required|exists:wards,id',
            'room_number' => 'required|string|max:255',
            'room_type' => 'required|in:regular,nursery',
        ]);

        Room::create([
            'ward_id' => $request->ward_id,
            'room_number' => $request->room_number,
            'room_type' => $request->room_type,
        ]);

        return redirect()->route('super-admin.ward-management')
            ->with('success', 'Room added successfully.');
    }

    /**
     * Add new bed to a room.
     */
    public function addBed(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'bed_number' => 'required|string|max:255',
            'bed_type' => 'required|in:regular,crib',
        ]);

        Bed::create([
            'room_id' => $request->room_id,
            'bed_number' => $request->bed_number,
            'bed_type' => $request->bed_type,
            'status' => 'Available',
        ]);

        return redirect()->route('super-admin.ward-management')
            ->with('success', 'Bed added successfully.');
    }

    /**
     * Delete a ward.
     */
    public function deleteWard(Ward $ward)
    {
        $this->checkSuperAdmin();

        $ward->delete();
        return redirect()->route('super-admin.ward-management')
            ->with('success', 'Ward deleted successfully.');
    }

    /**
     * Delete a room.
     */
    public function deleteRoom(Room $room)
    {
        $this->checkSuperAdmin();

        $room->delete();
        return redirect()->route('super-admin.ward-management')
            ->with('success', 'Room deleted successfully.');
    }

    /**
     * Delete a bed.
     */
    public function deleteBed(Bed $bed)
    {
        $this->checkSuperAdmin();

        $bed->delete();
        return redirect()->route('super-admin.ward-management')
            ->with('success', 'Bed deleted successfully.');
    }
}
