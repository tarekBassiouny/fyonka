<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\AmountMatchesType;
use App\Rules\SubtypeBelongsToType;

class TransactionAPIAddRequest extends FormRequest
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
            'date' => 'required|date',
            'store_id' => 'required|exists:stores,id',
            'type_id' => 'required|exists:transaction_types,id',
            'subtype_id' => [
                'required',
                'exists:transaction_subtypes,id',
                new SubtypeBelongsToType($this->input('type_id')),
            ],
            'source' => 'api'
        ];
    }
}
