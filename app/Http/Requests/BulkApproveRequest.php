<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkApproveRequest extends FormRequest
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
            'transactions' => 'required|array',
            'transactions.*.id' => 'required|integer|exists:transactions,id',
            'transactions.*.amount' => 'required|numeric',
            'transactions.*.description' => 'nullable|string',
            'transactions.*.date' => 'required|date',
            'transactions.*.store_id' => 'required|exists:stores,id',
            'transactions.*.type_id' => 'required|exists:transaction_types,id',
            'transactions.*.subtype_id' => 'required|exists:transaction_subtypes,id',
            'transactions.*.is_temp' => 'boolean',
        ];
    }
}
