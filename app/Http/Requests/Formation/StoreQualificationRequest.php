<?php

namespace App\Http\Requests\Formation;

use Illuminate\Foundation\Http\FormRequest;

class StoreQualificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Formation\Qualification::class);
    }

    public function rules(): array
    {
        return [
            'formation_id' => ['required', 'exists:formations,id'],
            'participant_id' => ['required', 'exists:participants,id'],
            'status' => ['required', 'in:qualifié,non qualifié,en attente,refusé'],
            'score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'date_evaluation' => ['nullable', 'date'],
            'observation' => ['nullable', 'string'],
            'evaluateur_id' => ['nullable', 'exists:instructeurs,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Le statut de qualification est obligatoire.',
            'score.max' => 'Le score ne peut pas dépasser 100.',
        ];
    }
}
