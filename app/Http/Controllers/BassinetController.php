<?php

namespace App\Http\Controllers;

use App\Models\Bassinet;
use App\Models\Room;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BassinetController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'bassinet_number' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Check if the room belongs to a maternity ward
            $room = Room::findOrFail($request->room_id);
            if ($room->ward->name !== 'Maternity Ward') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bassinets can only be added to maternity ward rooms.'
                ], 422);
            }

            // Check if bassinet number already exists in the room
            if (Bassinet::where('room_id', $request->room_id)
                ->where('bassinet_number', $request->bassinet_number)
                ->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'A bassinet with this number already exists in this room.'
                ], 422);
            }

            $bassinet = Bassinet::create([
                'room_id' => $request->room_id,
                'bassinet_number' => $request->bassinet_number,
                'status' => 'Available',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bassinet added successfully.',
                'bassinet' => $bassinet
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add bassinet: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Bassinet $bassinet)
    {
        try {
            DB::beginTransaction();

            // Check if the bassinet is occupied
            if ($bassinet->status !== 'Available') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete an occupied bassinet.'
                ], 422);
            }

            $bassinet->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bassinet deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bassinet: ' . $e->getMessage()
            ], 500);
        }
    }

    public function transfer(Request $request, Bassinet $bassinet)
    {
        $request->validate([
            'destination_ward_id' => 'required|exists:wards,id',
            'destination_room_id' => 'required|exists:rooms,id',
        ]);

        try {
            DB::beginTransaction();

            // Update bassinet's ward and room
            $bassinet->update([
                'ward_id' => $request->destination_ward_id,
                'room_id' => $request->destination_room_id,
                'status' => 'Transfer-in'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bassinet transferred successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to transfer bassinet: ' . $e->getMessage()
            ], 500);
        }
    }
} 