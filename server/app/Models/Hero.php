<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'ability', 
        'trainer_id', 
        'training_start_date', 
        'suit_colors', 
        'starting_power', 
        'current_power',
        'number_of_trainings',
    ];

    // Hero belongs to one trainer

    public function trainer() {
        return $this->belongsTo(Trainer::class, 'trainer_id', 'id');
    }
}
