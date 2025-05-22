<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class DashboardFilterRequest extends FormRequest
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
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'store_id' => 'nullable|exists:stores,id',
            'type_id' => 'nullable|exists:transaction_types,id',
            'subtype_id' => 'nullable|exists:transaction_subtypes,id',
            'per_page' => 'nullable|integer|min:1|max:100'
        ];
    }

    public function validatedFilters(): array
    {
        return array_filter($this->only([
            'date_from',
            'date_to',
            'store_id',
            'type_id',
            'subtype_id',
        ]), fn($value) => !is_null($value));
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $from = $this->input('date_from');
            $to = $this->input('date_to');

            if ($from && $to && Carbon::parse($from)->gt(Carbon::parse($to))) {
                $validator->errors()->add('date_to', __('validation.after_or_equal', ['attribute' => 'date from']));
            }
        });
    }
}
