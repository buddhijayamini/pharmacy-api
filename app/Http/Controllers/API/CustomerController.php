<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Customer::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|unique:customers,email|email',
                'phone' => 'required|string|max:255',
                'address' => 'required|string',
            ]);

            // Create the customer record
            $customer = Customer::create($validatedData);

            // Commit the transaction
            DB::commit();

            // Return the response with the created customer data
            return response()->json($customer, 201);
        } catch (ValidationException $e) {
            // If validation fails, rollback the transaction and return validation errors
            DB::rollBack();
            return response()->json($e->errors(), 422);
        } catch (Throwable $e) {
            // If any other error occurs, rollback the transaction and return a generic error response
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return $customer;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        try {
            DB::beginTransaction();
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:customers,email,' . $customer->id,
                'phone' => 'required|string|max:255',
                'address' => 'required|string',
            ]);

            // Update the customer record
            $customer->update($validatedData);

            // Commit the transaction
            DB::commit();

            // Return the response with the updated customer data
            return response()->json($customer, 200);
        } catch (ValidationException $e) {
            // If validation fails, rollback the transaction and return validation errors
            DB::rollBack();
            return response()->json($e->errors(), 422);
        } catch (Throwable $e) {
            // If any other error occurs, rollback the transaction and return a JSON response with only the error message
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function softDelete(Customer $customer)
    {
        try {
            DB::beginTransaction();
            // Soft delete the customer record
            $customer->delete();

            // Commit the transaction
            DB::commit();

            // Return a JSON response indicating success with status code 204
            return response()->json(null, 204);
        } catch (Throwable $e) {
            // If any error occurs, rollback the transaction and return a generic error response
            DB::rollBack();
            return response()->json(['message' =>  $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            DB::beginTransaction();
            // Permanently delete the customer record
            $customer->forceDelete();

            // Commit the transaction
            DB::commit();

            // Return a JSON response indicating success with status code 204
            return response()->json(null, 204);
        } catch (Throwable $e) {
            // If any error occurs, rollback the transaction and return a generic error response
            DB::rollBack();
            return response()->json(['message' =>  $e->getMessage()], 500);
        }
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();
            // Find the soft deleted customer record by its ID
            $customer = Customer::withTrashed()->find($id);

            // Check if the customer record exists and is soft deleted
            if ($customer && $customer->trashed()) {
                // Restore the customer record
                $customer->restore();

                // Commit the transaction
                DB::commit();

                // Return the restored customer record with a JSON response
                return response()->json($customer, 200);
            }

            // If the customer record is not found or is not soft deleted, return a 404 Not Found response
            return response()->json(null, 404);
        } catch (Throwable $e) {
            // If any error occurs, rollback the transaction and return a generic error response
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
