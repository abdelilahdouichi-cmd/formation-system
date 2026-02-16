<?php

namespace App\Http\Requests\Formation;

use Illuminate\Foundation\Http\FormRequest;

class StoreClasseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Formation\Classe::class);
    }

    public function rules(): array
    {
        return [
            'formation_id' => ['required', 'exists:formations,id'],
            'code' => ['required', 'string', 'max:50'],
            'nom' => ['required', 'string', 'max:255'],
            'capacite_maximal' => ['required', 'integer', 'min:1'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after:date_debut'],
            'lieu' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Le code de la classe est obligatoire.',
            'nom.required' => 'Le nom de la classe est obligatoire.',
            'capacite_maximal.required' => 'La capacité maximale est obligatoire.',
            'capacite_maximal.min' => 'La capacité doit être au minimum 1.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.after' => 'La date de fin doit être après la date de début.',
        ];
    }
}
