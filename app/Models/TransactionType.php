<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function subtypes()
    {
        return $this->hasMany(TransactionSubtype::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
