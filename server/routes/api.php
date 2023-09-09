<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HeroController;
use App\Http\Controllers\Api\TrainerController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function() {
    Route::put('/trainers/{id}', [TrainerController::class, 'updateTrainer']); // done
    Route::post('/logout', [AuthController::class, 'logout']); // done
});

Route::post('/signup', [AuthController::class, 'signup']); // done
Route::post('/login', [AuthController::class, 'login']); // done

Route::post('/createHero', [HeroController::class, 'createHero']); // done
Route::get('/heroes/{heroId}', [HeroController::class , 'getHero']); // done
Route::get('/heroes/by-trainer/{trainerId}', [HeroController::class, 'getHerosByTrainer']); // done
Route::put('/heroes/assign-to-trainer/{heroId}', [HeroController::class ,'assignToTrainer']); // done
Route::put('/heroes/unassign-from-trainer/{heroId}', [HeroController::class, 'unassignFromTrainer']); // done
Route::post('/heroes/train', [HeroController::class, 'trainHero']); // done - BONUS

Route::get('/trainers/{id}', [TrainerController::class, 'getTrainer']); // done
