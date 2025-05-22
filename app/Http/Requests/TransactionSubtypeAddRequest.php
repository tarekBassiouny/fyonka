<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionSubtypeAddRequest extends FormRequest
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
            'name' => 'required|string|unique:transaction_subtypes,name,' . ($this->transaction_subtype?->id ? ',' . $this->transaction_subtype->id : ''),
            'transaction_type_id' => 'required|exists:transaction_types,id',
        ];
    }
}
