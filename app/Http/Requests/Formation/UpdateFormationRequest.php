<?php

namespace App\Http\Requests\Formation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->formation);
    }

    public function rules(): array
    {
        return [
            'niveau_id' => ['nullable', 'exists:niveaux,id'],
            'categorie_id' => ['nullable', 'exists:categories,id'],
            'sous_categorie_id' => ['nullable', 'exists:sous_categories,id'],
            'nom' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100', Rule::unique('formations')->ignore($this->formation->id)],
            'description' => ['nullable', 'string'],
            'objectif' => ['nullable', 'string'],
            'duree_heures' => ['nullable', 'integer', 'min:1'],
            'prix' => ['nullable', 'numeric', 'min:0'],
            'date_debut_prevue' => ['nullable', 'date'],
            'date_fin_prevue' => ['nullable', 'date', 'after:date_debut_prevue'],
            'date_debut_reelle' => ['nullable', 'date'],
            'date_fin_reelle' => ['nullable', 'date', 'after:date_debut_reelle'],
            'status' => ['in:planifiée,à programmer,exécutée,annulée,archivée'],
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
            'date_fin_prevue.after' => 'La date de fin prévue doit être après la date de début.',
            'date_fin_reelle.after' => 'La date de fin réelle doit être après la date de début.',
        ];
    }
}
