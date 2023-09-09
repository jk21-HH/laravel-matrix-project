<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\Controller;

use App\Models\Hero;
use App\Models\Trainer;

use App\Http\Requests\CreateHeroRequest;

use Carbon\Carbon;

class HeroController extends Controller
{
    /**
     * Create a new hero
     *
     * @param  CreateHeroRequest  $request
     * @return JsonResponse
     */

    public function createHero(CreateHeroRequest $request): JsonResponse
    {
        // Validated by the CreateHeroRequest
        
        $validatedData = $request->validated();

        $existingHero = Hero::where('name', $request->input('name'))->first();

        if ($existingHero) {

            // Return 422 Unprocessable Entity status code

            return response()->json([
                'status' => 'fail',
                'message' => 'Hero already exists.'
            ], 422); 
        } 


        // Create a new hero using the validated data
        
        $hero = Hero::create($validatedData);

        // OK status code

        return response()->json([
            'status' => 'success',
            'hero' => $hero,
            'message' => 'Hero created successfully'
        ], 201);
    }

    /**
     * Get the data of a specific hero
     *
     * @param  int  $heroId
     * @return JsonResponse
     */

    public function getHero(int $heroId): JsonResponse
    {
        // Find the hero by id
        
        $hero = Hero::find($heroId);

        if (!$hero) {

            // Return 404 Not Found status code

            return response()->json([
                'status' => 'fail',
                'message' => 'Hero not found'
            ], 404);
        }

        // OK status code

        return response()->json([
            'status' => 'success',
            'data' => $hero
        ], 200);
    }

    /**
     * Get heroes of a specific trainer
     *
     * @param  int  $trainerId
     * @return JsonResponse
     */

    public function getHerosByTrainer(int $trainerId): JsonResponse
    {
        // Get heroes by trainer_id
        
        $heroes = Hero::where('trainer_id', $trainerId)->get();

        // OK status code

        return response()->json([
            'status' => 'success',
            'data' => $heroes
        ], 200);
    }

     /**
     * Assign a hero to a trainer
     *
     * @param Request $request
     * @param int $heroId
     * @return JsonResponse
     */

    public function assignToTrainer(Request $request, int $heroId): JsonResponse
    {
        // Validate the incoming JSON request.
        
        $request->validate([
            'trainer_id' => 'required|exists:trainers,id',
        ]);

        $trainerId = $request->input('trainer_id');

        // // Check if trainer exists
        
        if (!$this->trainerExists($trainerId)) {

            // Return 404 Not Found status code

            return response()->json([
                'status' => 'fail',
                'message' => 'Trainer not found'
            ], 404);
        }

        // Find the hero by ID
        
        $hero = Hero::find($heroId);

        if (!$hero) {

            // Return 404 Not Found status code

            return response()->json([
                'status' => 'fail',
                'message' => 'Hero not found'
            ], 404);
        }

        //  Updating the trainer_id in order to assign hero to trainer
        
        $hero->trainer_id = $trainerId;
        $hero->save();

        // OK status code

        return response()->json([
            'status' => 'success',
            'message' => 'Hero assigned to trainer successfully'
        ], 201);
    }

    /**
     * Check if a trainer exists.
     *
     * @param  int  $trainerId
     * @return bool
     */

    private function trainerExists(int $trainerId): bool
    {
        return Trainer::where('id', $trainerId)->exists();
    }

    /**
     * Unassign a hero from a trainer
     *
     * @param  int  $trainerId
     * @param  int  $heroId
     * @return JsonResponse
     */

    public function unassignFromTrainer(int $heroId): JsonResponse
    {
        // Find the hero by id
        
        $hero = Hero::find($heroId);

        if (!$hero) {

            // Return 404 Not Found status code
            
            return response()->json([
                'status' => 'fail',
                'message' => 'Hero not found'
            ], 404);
        }

        //  Updating the trainer_id in order to unassign hero to trainer
        
        $hero->trainer_id = null;
        $hero->save();

        // OK status code

        return response()->json([
            'status' => 'success',
            'message' => 'Hero unassigned from trainer successfully'
        ], 201);
    }

    /**
    * Check if a hero can be trained currently
    *
    * @param  Request  $request
    * @return JsonResponse
    */

    public function trainHero(Request $request) : JsonResponse
    {
        // Validate JSON

        $request->validate([
            'hero_id' => 'required|exists:heroes,id',
        ]);

        $heroId = $request->input('hero_id');

        // Find the hero by id

        $hero = Hero::find($heroId);

        if (!$hero) {

            // Return 404 Not Found status code

            return response()->json([
                'status' => 'fail',
                'message' => 'Hero not found'
            ], 404);
        }
        
        $canTrain = $this->canHeroTrain($hero);

        if (!$canTrain) {

            // Return a response indicating that hero can not train

            return response()->json([
                'status' => 'fail',
                'message' => 'Hero cannot train at this time'
            ], 400);
        }

        // Calculate the power increase

        $powerIncrease = mt_rand(0, 10) / 100;

        // Update hero current power

        $hero->current_power += $hero->current_power * $powerIncrease;

        // Update the number_of_trainings and the last_trained_at timestamp

        $hero->number_of_trainings += 1;

        // Save the updated hero data

        $hero->save();

        // OK status code

        return response()->json([
            'status' => 'success',
            'message' => 'Hero trained successfully',
            'power_increase' => $powerIncrease,
            'new_power' => $hero->current_power,
            'number_of_trainings' => $hero->number_of_trainings
        ], 200);
    }

    /**
    * Check if a hero can train based on the defined conditions.
    *
    * @param  Hero  $hero
    * @return bool
    */

    private function canHeroTrain(Hero $hero): bool
    {
        $isValid = true;
        
        if((Carbon::now()->diffInHours($hero->updated_at) < 24 && $this->isDividedByFive($hero->number_of_trainings)) && $hero->number_of_trainings !== 0) {
            $isValid = false;
        }

        return $isValid;
    }

    /**
    * Check if number is divided by 5
    *
    * @param  int  $num
    * @return bool
    */

    private function isDividedByFive(int $num) : bool
    {
        return ($num % 5 === 0);
    }
}