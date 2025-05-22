<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\TransactionSubtype;

class SubtypeBelongsToType implements ValidationRule
{
    private $typeId;

    public function __construct($typeId)
    {
        $this->typeId = $typeId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $subtype = TransactionSubtype::find($value);

        if (! $subtype || $subtype->transaction_type_id !== (int) $this->typeId) {
            $fail(__('transaction.invalid_subtype_for_type'));
        }
    }
}
