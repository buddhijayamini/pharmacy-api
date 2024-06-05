<?php

namespace App\Http\Controllers\API;

use App\Models\Medication;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class MedicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medications = Medication::all();
        return response()->json(['medications' => $medications]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            DB::beginTransaction();

            $cusId = (int)$request->customer_id;

            // Add user_id to validated data
            $validatedData = $validator->validated();
            $validatedData['user_id'] = Auth::id();
            $validatedData['customer_id'] = $cusId;

            // Create the medication record
            $medication = Medication::create($validatedData);

            // Commit the transaction
            DB::commit();

            // Return the response
            return response()->json($medication, 201);
        }catch (Throwable $e) {
            // If any other error occurs, rollback the transaction and return a generic error response
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $medication = Medication::find($id);
        if (!$medication) {
            return response()->json(['error' => 'Medication not found'], 404);
        }
        return response()->json(['medication' => $medication]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medication $medication)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
            ]);


            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            DB::beginTransaction();

            // Update the medication record
            $medication->update($validator->validated());

            // Commit the transaction
            DB::commit();

            // Return the response
            return response()->json($medication, 200);
        } catch (Throwable $e) {
            // If any other error occurs, rollback the transaction and return a generic error response
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function softDelete(Medication $medication)
    {
        try {
            DB::beginTransaction();

            // Soft delete the medication record
            $medication->delete();

            // Commit the transaction
            DB::commit();

            // Return a JSON response indicating success with status code 204
            return response()->json(null, 204);
        } catch (Throwable $e) {
            // If any error occurs, rollback the transaction and return a generic error response
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medication $medication)
    {
        try {
            DB::beginTransaction();
            // Permanently delete the medication record
            $medication->forceDelete();

            // Commit the transaction
            DB::commit();

            // Return a JSON response indicating success with status code 204
            return response()->json(null, 204);
        } catch (Throwable $e) {
            // If any error occurs, rollback the transaction and return a generic error response
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();

            // Find the soft deleted medication record by its ID
            $medication = Medication::withTrashed()->find($id);

            // Check if the medication record exists and is soft deleted
            if ($medication && $medication->trashed()) {
                // Restore the medication record
                $medication->restore();

                // Commit the transaction
                DB::commit();

                // Return the restored medication record with a JSON response
                return response()->json($medication, 200);
            }

            // If the medication record is not found or is not soft deleted, return a 404 Not Found response
            return response()->json(null, 404);
        } catch (Throwable $e) {
            // If any error occurs, rollback the transaction and return a generic error response
            DB::rollBack();
            return response()->json(['message' =>  $e->getMessage()], 500);
        }
    }
}
