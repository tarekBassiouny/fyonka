<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\TransactionType;

class AmountMatchesType implements ValidationRule
{
    protected $typeId;

    public function __construct($typeId)
    {
        $this->typeId = $typeId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $type = TransactionType::find($this->typeId);

        if (! $type) return;

        if ($type->name === 'outcome' && $value > 0) {
            $fail(__('transaction.amount_must_be_negative'));
        }

        if ($type->name === 'income' && $value < 0) {
            $fail(__('transaction.amount_must_be_positive'));
        }
    }
}
