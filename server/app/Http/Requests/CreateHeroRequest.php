<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateHeroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'ability' => 'integer',
            'trainer_id' => 'integer',
            'training_start_date' => 'date',
            'suit_colors' => 'integer', 
            'starting_power' => 'numeric',
            'current_power' => 'numeric', 
        ];
    }
}
