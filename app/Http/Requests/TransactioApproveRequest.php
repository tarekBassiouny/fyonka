<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactioApproveRequest extends FormRequest
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
            'type_id' => 'required|exists:transaction_types,id',
            'subtype_id' => 'required|exists:transaction_subtypes,id',
        ];
    }
}
