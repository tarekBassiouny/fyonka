<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model
{
    use HasFactory;

    protected $fillable = ['filename', 'path'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
