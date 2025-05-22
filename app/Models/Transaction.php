<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'amount',
        'description',
        'type',
        'type_id',
        'date',
        'store_id',
        'subtype_id',
        'is_temp',
        'source',
        'source_detail',
        'ordering_account',
        'booking_date',
        'value_date',
        'booking_text',
        'purpose',
        'creditor_id',
        'mandate_reference',
        'customer_reference',
        'batch_reference',
        'original_debit_amount',
        'refund_fee',
        'beneficiary',
        'iban',
        'bic',
        'currency',
        'note',
        'uploaded_file_id'
    ];

    protected $casts = [
        'date' => 'date',
        'booking_date' => 'date',
        'value_date' => 'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'type_id');
    }

    public function subtype()
    {
        return $this->belongsTo(TransactionSubtype::class, 'subtype_id');
    }

    public function uploadedFile()
    {
        return $this->belongsTo(UploadedFile::class);
    }
}
