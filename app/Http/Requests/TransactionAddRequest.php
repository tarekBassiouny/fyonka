<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionAddRequest extends FormRequest
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
            'date' => 'required|date',
            'store_id' => 'required|exists:stores,id',
            'type_id' => 'nullable|exists:transaction_types,id',
            'subtype_id' => 'nullable|exists:transaction_subtypes,id',
            'is_temp' => 'boolean',

            // extra fields
            'ordering_account' => 'nullable|string',
            'booking_date' => 'nullable|date',
            'value_date' => 'nullable|date',
            'booking_text' => 'nullable|string',
            'purpose' => 'nullable|string',
            'creditor_id' => 'nullable|string',
            'mandate_reference' => 'nullable|string',
            'customer_reference' => 'nullable|string',
            'batch_reference' => 'nullable|string',
            'original_debit_amount' => 'nullable|string',
            'refund_fee' => 'nullable|string',
            'beneficiary' => 'nullable|string',
            'iban' => 'nullable|string',
            'bic' => 'nullable|string',
            'currency' => 'nullable|string',
            'note' => 'nullable|string',
            'uploaded_file_id' => 'nullable|exists:uploaded_files,id',
        ];
    }
}
