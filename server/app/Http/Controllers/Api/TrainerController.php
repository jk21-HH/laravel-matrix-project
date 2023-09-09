<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Controller;

use App\Models\Trainer;

class TrainerController extends Controller
{
    /**
     * Get the data of a specific trainer
     *
     * @param  int  $trainerId
     * @return JsonResponse
     */

    public function getTrainer(int $trainerId): JsonResponse
    {
        // Find the trainer by id
        
        $trainer = Trainer::find($trainerId);

        if (!$trainer) {
            
            // Return 404 Not Found status code

            return response()->json([
                'status' => 'fail',
                'message' => 'Trainer not found'
            ], 404);
        }

        // OK status code

        return response()->json([
            'status' => 'success',
            'data' => $trainer
        ], 200);
    }

     /**
     * Update the trainer's own data.
     *
     * @param  Request $request
     * @param  int  $trainerId 
     * @return JsonResponse
     */

    public function updateTrainer(Request $request, int $trainerId): JsonResponse
    {
        // Get the authenticated trainer

        $authenticatedTrainer = Auth::user();

        // Check if the authenticated trainer matches the trainer being updated

        if ($authenticatedTrainer->id !== $trainerId) {

            // Return 403 Forbidden

            return response()->json([
                'status' => 'fail',
                'message' => 'You are not authorized to update this trainer data'
            ], 403);
        }

        // Validate the request data using the custom validation method

        try {
            $validatedData = $this->validateUpdateRequest($request, $authenticatedTrainer);
        } catch (\Exception $e) {
            
            // Return 400 Bad Request
            
            return response()->json([
                'status' => 'fail',
                'message' => 'At least one of the fields is required',
            ], 400);
        }

        // Update the fields of the trainer

        $trainer = Trainer::find($trainerId);

        if (!$trainer) {

            // Return 404 Not Found status code

            return response()->json([
                'status' => 'fail',
                'message' => 'Trainer not found'
            ], 404);
        }

        // Hash the new password with bcrypt if provided

        if ($request->filled('password')) {
            $validatedData['password'] = \Hash::make($validatedData['password']);
        }

        // Update the trainer's data

        $trainer->update($validatedData);

        // OK status code

        return response()->json([
            'status' => 'success',
            'message' => 'Trainer data updated successfully',
            'data' => $trainer
        ], 200);
    }

    /**
     * Validate the update request.
     *
     * @param  Request $request
     * @param  \App\Models\Trainer  $authenticatedTrainer
     * @return array
     * @throws \Exception
     */

    private function validateUpdateRequest(Request $request, Trainer $authenticatedTrainer): array
    {
        // Define validation rules

        $rules = [];

        // Check if at least one of the fields (email, full_name, password) is provided in the request

        if ($request->filled('email') || $request->filled('full_name') || $request->filled('password')) {

            // Email field should be unique if updated

            $rules['email'] = [
                'email',
                Rule::unique('trainers')->ignore($authenticatedTrainer->id),
                function ($attribute, $value, $fail) use ($authenticatedTrainer) {
                    if ($value === $authenticatedTrainer->email) {
                        $fail('Email must be different from your current email');
                    }
                },
            ];

            // Full Name field should be required if updated

            $rules['full_name'] = 'string|max:255';

            // Password field should be required if updated and different from the current password

            $rules['password'] = [
                'string',
                'min:8',
                function ($attribute, $value, $fail) use ($authenticatedTrainer) {
                    if (\Hash::check($value, $authenticatedTrainer->password)) {
                        $fail('Password must be different from your current Password');
                    }
                },
            ];
        } else {

            // If none of the fields are provided, throw a regular exception
            
            throw new \Exception('At least one of the fields is required');
        }

        // Validate the request data
        
        return $request->validate($rules);
    }
}