<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\AmountMatchesType;
use App\Rules\SubtypeBelongsToType;

class TransactionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                new AmountMatchesType($this->input('type_id')),
            ],
            'description' => 'nullable|string',
            'type' => 'required|in:income,outcome',
            'date' => 'required|date',
            'store_id' => 'sometimes|exists:stores,id',
            'type_id' => 'sometimes|exists:transaction_types,id',
            'subtype_id' => [
                'required',
                'exists:transaction_subtypes,id',
                new SubtypeBelongsToType($this->input('type_id')),
            ],
            'is_temp' => 'boolean',
        ];
    }
}
