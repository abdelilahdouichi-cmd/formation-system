<?php

namespace App\Http\Requests\Formation;

use Illuminate\Foundation\Http\FormRequest;

class StoreJustificatifRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Formation\Justificatif::class);
    }

    public function rules(): array
    {
        return [
            'formation_id' => ['required', 'exists:formations,id'],
            'participant_id' => ['required', 'exists:participants,id'],
            'diplome_id' => ['nullable', 'exists:diplomes,id'],
            'licence_id' => ['nullable', 'exists:licences,id'],
            'type_document' => ['required', 'string', 'in:attestation,certificat,relevé,autre'],
            'nom_fichier' => ['required', 'string', 'max:255'],
            'chemin_fichier' => ['required', 'string', 'max:500'],
            'taille_fichier' => ['required', 'integer', 'min:1'],
            'date_ajout' => ['required', 'date'],
            'is_verified' => ['nullable', 'boolean'],
            'verified_at' => ['nullable', 'date'],
            'remarques' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'type_document.required' => 'Le type de document est obligatoire.',
            'type_document.in' => 'Le type de document doit être l\'un des types autorisés.',
            'nom_fichier.required' => 'Le nom du fichier est obligatoire.',
            'chemin_fichier.required' => 'Le chemin du fichier est obligatoire.',
            'taille_fichier.required' => 'La taille du fichier est obligatoire.',
            'date_ajout.required' => 'La date d\'ajout est obligatoire.',
        ];
    }
}
