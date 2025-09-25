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
use Illuminate\Support\Facades\DB;

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
        try {
            DB::beginTransaction();

            $request->validate([
                'ward_id' => 'required|exists:wards,id',
                'room_name' => 'required|string|max:255',
                'room_type' => 'required|in:regular,nursery',
                'capacity' => 'nullable|integer|min:1|max:50',
            ]);

            $ward = Ward::findOrFail($request->ward_id);
            
            // Get the highest sequence number for this ward
            $maxSequence = Room::where('ward_id', $ward->id)->max('sequence');
            $newSequence = ($maxSequence ?? 0) + 1;

            $room = Room::create([
                'ward_id' => $ward->id,
                'room_name' => $request->room_name,
                'room_type' => $request->room_type,
                'sequence' => $newSequence,
                'capacity' => $request->capacity ?? 2,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Room added successfully.',
                'room' => $room
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add room: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add new bed to a room.
     */
    public function addBed(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $this->checkSuperAdmin();

            $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'bed_number' => 'required|string|max:255',
                'bed_type' => 'required|in:regular,crib',
            ]);

            $bed = Bed::create([
                'room_id' => $request->room_id,
                'bed_number' => $request->bed_number,
                'bed_type' => $request->bed_type,
                'status' => 'Available',
            ]);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bed added successfully.',
                'bed' => $bed
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add bed: ' . $e->getMessage()
            ], 500);
        }
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
        try {
            DB::beginTransaction();
            
            $this->checkSuperAdmin();

            // Get the room and ward info for redirecting back with the correct state
            $roomId = $bed->room_id;
            $wardId = $bed->room->ward_id;
            
            $bed->delete();
            
            DB::commit();
            
            // Redirect back to the ward management page with a success message
            return redirect()->route('super-admin.ward-management')
                ->with('success', 'Bed deleted successfully.')
                ->with('expanded_ward', $wardId)
                ->with('expanded_room', $roomId);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('super-admin.ward-management')
                ->with('error', 'Failed to delete bed: ' . $e->getMessage());
        }
    }
}
