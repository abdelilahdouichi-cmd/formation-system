<?php

namespace App\Http\Requests\Formation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDiplomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Formation\Diplome::class);
    }

    public function rules(): array
    {
        return [
            'formation_id' => ['required', 'exists:formations,id'],
            'participant_id' => ['required', 'exists:participants,id'],
            'numero_diplome' => ['required', 'string', Rule::unique('diplomes', 'numero_diplome')],
            'date_emission' => ['required', 'date'],
            'date_validite' => ['nullable', 'date', 'after_or_equal:date_emission'],
            'lieu_emission' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:livrée,à examiner,à livrer,refusée,annulée'],
            'remarques' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'numero_diplome.required' => 'Le numéro de diplôme est obligatoire.',
            'numero_diplome.unique' => 'Ce numéro de diplôme existe déjà.',
            'date_emission.required' => 'La date d\'émission est obligatoire.',
            'date_validite.after_or_equal' => 'La date de validité ne peut pas être avant la date d\'émission.',
            'status.required' => 'Le statut du diplôme est obligatoire.',
        ];
    }
}
