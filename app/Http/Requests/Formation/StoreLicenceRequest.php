<?php

namespace App\Http\Requests\Formation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLicenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Formation\Licence::class);
    }

    public function rules(): array
    {
        return [
            'formation_id' => ['required', 'exists:formations,id'],
            'participant_id' => ['required', 'exists:participants,id'],
            'numero_licence' => ['required', 'string', Rule::unique('licences', 'numero_licence')],
            'date_emission' => ['required', 'date'],
            'date_expiration' => ['required', 'date', 'after:date_emission'],
            'lieu_emission' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:livrée,à livrer,suspendue,expirée,renouvelée'],
            'remarques' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'numero_licence.required' => 'Le numéro de licence est obligatoire.',
            'numero_licence.unique' => 'Ce numéro de licence existe déjà.',
            'date_emission.required' => 'La date d\'émission est obligatoire.',
            'date_expiration.required' => 'La date d\'expiration est obligatoire.',
            'date_expiration.after' => 'La date d\'expiration doit être après la date d\'émission.',
            'status.required' => 'Le statut de la licence est obligatoire.',
        ];
    }
}
