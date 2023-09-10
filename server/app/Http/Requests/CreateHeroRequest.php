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
            'ability' => 'numeric|min:0|max:1',
            'trainer_id' => 'integer',
            'training_start_date' => 'date',
            'suit_colors' => 'string', 
            'starting_power' => 'numeric',
            'current_power' => 'numeric', 
        ];
    }
}
