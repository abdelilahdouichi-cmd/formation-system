<?php

namespace App\Http\Requests\Formation;

use Illuminate\Foundation\Http\FormRequest;

class StoreParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Formation\Participant::class);
    }

    public function rules(): array
    {
        return [
            'formation_id' => ['nullable', 'exists:formations,id'],
            'classe_id' => ['nullable', 'exists:classes,id'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:participants,email'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'entreprise' => ['nullable', 'string', 'max:255'],
            'poste' => ['nullable', 'string', 'max:255'],
            'numero_identite' => ['nullable', 'string', 'max:50', 'unique:participants,numero_identite'],
            'date_inscription' => ['nullable', 'date'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after:date_debut'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Un participant avec cet email existe déjà.',
            'numero_identite.unique' => 'Ce numéro d\'identité est déjà utilisé.',
            'date_fin.after' => 'La date de fin doit être après la date de début.',
        ];
    }
}
