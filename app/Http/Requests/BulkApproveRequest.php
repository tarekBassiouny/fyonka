<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\AmountMatchesType;
use App\Models\TransactionSubtype;

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
            'transactions.*.amount' => [
                'required',
                'numeric',
                new AmountMatchesType($this->input('type_id')),
            ],
            'transactions.*.description' => 'nullable|string',
            'transactions.*.date' => 'required|date',
            'transactions.*.store_id' => 'required|exists:stores,id',
            'transactions.*.type_id' => 'required|exists:transaction_types,id',
            'transactions.*.subtype_id' => [
                'required',
                'exists:transaction_subtypes,id',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $typeId = $this->input("transactions.$index.type_id");

                    if (!TransactionSubtype::where('id', $value)
                        ->where('transaction_type_id', $typeId)
                        ->exists()) {
                        $fail(__('transaction.invalid_subtype_for_type'));
                    }
                },
            ],
            'transactions.*.is_temp' => 'boolean',
        ];
    }
}
