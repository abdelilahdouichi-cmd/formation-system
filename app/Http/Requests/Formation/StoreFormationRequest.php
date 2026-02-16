<?php

namespace App\Http\Requests\Formation;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Formation\Formation::class);
    }

    public function rules(): array
    {
        return [
            'niveau_id' => ['nullable', 'exists:niveaux,id'],
            'categorie_id' => ['nullable', 'exists:categories,id'],
            'sous_categorie_id' => ['nullable', 'exists:sous_categories,id'],
            'nom' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100', 'unique:formations,code'],
            'description' => ['nullable', 'string'],
            'objectif' => ['nullable', 'string'],
            'duree_heures' => ['nullable', 'integer', 'min:1'],
            'prix' => ['nullable', 'numeric', 'min:0'],
            'date_debut_prevue' => ['nullable', 'date'],
            'date_fin_prevue' => ['nullable', 'date', 'after:date_debut_prevue'],
            'lieu' => ['nullable', 'string', 'max:255'],
            'nombreParticipantsMin' => ['nullable', 'integer', 'min:1'],
            'nombreParticipantsMax' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'Ce code de formation existe déjà.',
            'date_fin_prevue.after' => 'La date de fin doit être après la date de début.',
            'nombreParticipantsMax.min' => 'Le nombre maximum doit être au moins 1.',
        ];
    }
}
