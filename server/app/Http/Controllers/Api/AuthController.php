<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Models\Trainer;

class AuthController extends Controller
{
    /**
     * Register a new Trainer.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function signup(Request $request) : JsonResponse
    {
        $validatedData = $request->validate([
            'email' => 'required',
            'full_name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        $existingTrainer = Trainer::where('email', $request->input('email'))->first();

        if ($existingTrainer) {

            // Return 422 Unprocessable Entity status code

            return response()->json([
                'status' => 'fail',
                'message' => 'Email already exists.'
            ], 422); 
        } 

        $trainer = Trainer::create([
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'full_name' => $validatedData['full_name'],
        ]);
    
        $token = $trainer->createToken('main')->plainTextToken;
    
        // OK status code

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'trainer' => $trainer,
            'message' => 'Trainer registered successfully'
        ], 201);
    }


    /**
     * Log in a Trainer and return a JWT token.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request) : JsonResponse
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]); 

        // Attempt to authenticate the trainer
        
        if (!auth()->attempt($validatedData)) {

            // Return 401 Unauthorized status code

            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid credentials',
            ], 401);
        }   

        // Authentication successful, get the authenticated trainer
        
        $trainer = auth()->user();  

        // Generate a new token for the trainer
        
        $token = $trainer->createToken('main')->plainTextToken; 

        // OK status code

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'trainer' => $trainer,
            'message' => 'Trainer logged in successfully'
        ], 200); 
    }

    /**
     * Log out a Trainer (invalidate the JWT token).
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout(Request $request) : JsonResponse
    {        
        $request->user()->currentAccessToken()->delete();

        // OK status code

        return response()->json([
                'status' => 'success',
                'message' => 'Trainer logged out successfully'
            ], 200);
    }
}
