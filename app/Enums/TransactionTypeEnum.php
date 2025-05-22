<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case INCOME = 'income';
    case OUTCOME = 'outcome';

    public static function idMap(): array
    {
        return [
            self::INCOME->value => 1,   // ID for 'income'
            self::OUTCOME->value => 2,  // ID for 'outcome'
        ];
    }

    public static function id(string $value): ?int
    {
        return self::idMap()[$value] ?? null;
    }
}
