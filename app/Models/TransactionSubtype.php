<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSubtype extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'transaction_type_id'];

    public function type()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }
}
