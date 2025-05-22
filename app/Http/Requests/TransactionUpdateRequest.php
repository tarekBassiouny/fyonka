<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'type' => 'required|in:income,outcome',
            'date' => 'required|date',
            'store_id' => 'sometimes|exists:stores,id',
            'type_id' => 'sometimes|exists:transaction_types,id',
            'subtype_id' => 'sometimes|exists:transaction_subtypes,id',
            'is_temp' => 'boolean',
        ];
    }
}
