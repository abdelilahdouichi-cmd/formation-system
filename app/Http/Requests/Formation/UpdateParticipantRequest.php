<?php

namespace App\Http\Requests\Formation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->participant);
    }

    public function rules(): array
    {
        return [
            'classe_id' => ['nullable', 'exists:classes,id'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('participants')->ignore($this->participant->id)],
            'telephone' => ['nullable', 'string', 'max:20'],
            'entreprise' => ['nullable', 'string', 'max:255'],
            'poste' => ['nullable', 'string', 'max:255'],
            'numero_identite' => ['nullable', 'string', 'max:50', Rule::unique('participants')->ignore($this->participant->id)],
            'date_inscription' => ['nullable', 'date'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after:date_debut'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Un autre participant avec cet email existe.',
            'numero_identite.unique' => 'Ce numéro d\'identité est déjà utilisé.',
            'date_fin.after' => 'La date de fin doit être après la date de début.',
        ];
    }
}
