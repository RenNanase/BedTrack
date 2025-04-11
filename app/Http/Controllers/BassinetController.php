<?php

namespace App\Http\Controllers;

use App\Models\Bassinet;
use App\Models\Room;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\TransferLog;
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
            if ($room->ward->ward_name !== 'Maternity Ward') {
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
            'destination_crib_id' => 'required|exists:beds,id',
        ]);

        try {
            DB::beginTransaction();

            // Get the destination ward
            $destinationWard = Ward::findOrFail($request->destination_ward_id);
            
            // Verify it's a nursery ward
            if (!$destinationWard->is_nursery && $destinationWard->ward_name !== 'Nursery Ward' && $destinationWard->ward_name !== 'Nursery') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bassinets can only be transferred to a Nursery Ward.'
                ], 422);
            }

            // Get the destination crib
            $destinationCrib = Bed::findOrFail($request->destination_crib_id);
            
            // Verify the crib is available
            if ($destinationCrib->status !== 'Available') {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected crib is not available.'
                ], 422);
            }

            // Create transfer log
            TransferLog::create([
                'source_bassinet_id' => $bassinet->id,
                'source_room_id' => $bassinet->room_id,
                'destination_bed_id' => $destinationCrib->id,
                'destination_room_id' => $destinationCrib->room_id,
                'patient_name' => $bassinet->patient_name,
                'patient_category' => 'Paediatric',
                'gender' => $bassinet->gender,
                'mrn' => $bassinet->mrn,
                'transferred_at' => now(),
                'transfer_remarks' => "Transferred from Maternity Ward Bassinet {$bassinet->bassinet_number} to Nursery Ward Crib {$destinationCrib->bed_number}"
            ]);

            // Update the destination crib
            $destinationCrib->update([
                'status' => 'Occupied',
                'patient_name' => $bassinet->patient_name,
                'gender' => $bassinet->gender,
                'mrn' => $bassinet->mrn,
                'patient_category' => 'Paediatric',
                'status_changed_at' => now(),
            ]);

            // Clear the bassinet
            $bassinet->update([
                'status' => 'Available',
                'patient_name' => null,
                'gender' => null,
                'mrn' => null,
                'mother_name' => null,
                'mother_mrn' => null,
                'occupied_at' => null,
                'status_changed_at' => now(),
            ]);

            // Log the activity
            activity()
                ->performedOn($bassinet)
                ->causedBy(auth()->user())
                ->withProperties([
                    'baby_name' => $bassinet->patient_name,
                    'from_bassinet' => $bassinet->bassinet_number,
                    'to_crib' => $destinationCrib->bed_number
                ])
                ->log('transferred baby from bassinet to nursery crib');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Baby transferred successfully to nursery ward',
                'bassinet' => $bassinet->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to transfer baby: ' . $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request, Bassinet $bassinet)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'baby_name' => 'required|string|max:255',
                'baby_gender' => 'required|in:Male,Female',
                'baby_mrn' => 'required|string|max:255',
                'mother_name' => 'required|string|max:255',
                'mother_mrn' => 'required|string|max:255',
                'notes' => 'nullable|string',
            ]);

            // Check if bassinet is available
            if ($bassinet->status !== 'Available') {
                return response()->json([
                    'success' => false,
                    'error' => 'This bassinet is not available for registration'
                ], 400);
            }

            // Update bassinet with baby information
            $bassinet->update([
                'status' => 'Occupied',
                'patient_name' => $validated['baby_name'],
                'gender' => $validated['baby_gender'],
                'mrn' => $validated['baby_mrn'],
                'mother_name' => $validated['mother_name'],
                'mother_mrn' => $validated['mother_mrn'],
                'notes' => $validated['notes'],
                'occupied_at' => now(),
                'status_changed_at' => now(),
            ]);

            // Log the activity
            activity()
                ->performedOn($bassinet)
                ->causedBy(auth()->user())
                ->withProperties([
                    'baby_name' => $validated['baby_name'],
                    'baby_mrn' => $validated['baby_mrn'],
                    'mother_name' => $validated['mother_name'],
                    'mother_mrn' => $validated['mother_mrn']
                ])
                ->log('registered baby in bassinet');

            // Get the fresh bassinet data with all relationships
            $updatedBassinet = $bassinet->fresh();

            return response()->json([
                'success' => true,
                'message' => 'Baby registered successfully',
                'bassinet' => [
                    'id' => $updatedBassinet->id,
                    'bassinet_number' => $updatedBassinet->bassinet_number,
                    'status' => $updatedBassinet->status,
                    'patient_name' => $updatedBassinet->patient_name,
                    'gender' => $updatedBassinet->gender,
                    'mrn' => $updatedBassinet->mrn,
                    'mother_name' => $updatedBassinet->mother_name,
                    'mother_mrn' => $updatedBassinet->mother_mrn,
                    'notes' => $updatedBassinet->notes,
                    'occupied_at' => $updatedBassinet->occupied_at,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to register baby: ' . $e->getMessage()
            ], 500);
        }
    }

    public function discharge(Bassinet $bassinet)
    {
        try {
            DB::beginTransaction();

            // Check if bassinet is occupied
            if ($bassinet->status !== 'Occupied') {
                return response()->json([
                    'success' => false,
                    'message' => 'This bassinet is not occupied.'
                ], 422);
            }

            // Log the discharge activity
            activity()
                ->performedOn($bassinet)
                ->causedBy(auth()->user())
                ->withProperties([
                    'baby_name' => $bassinet->patient_name,
                    'baby_mrn' => $bassinet->mrn,
                    'mother_name' => $bassinet->mother_name,
                    'mother_mrn' => $bassinet->mother_mrn
                ])
                ->log('discharged baby from bassinet');

            // Clear the bassinet
            $bassinet->update([
                'status' => 'Available',
                'patient_name' => null,
                'gender' => null,
                'mrn' => null,
                'mother_name' => null,
                'mother_mrn' => null,
                'occupied_at' => null,
                'status_changed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Baby discharged successfully',
                'bassinet' => $bassinet->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to discharge baby: ' . $e->getMessage()
            ], 500);
        }
    }
} 