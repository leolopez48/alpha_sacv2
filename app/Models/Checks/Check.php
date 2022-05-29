<?php

namespace App\Models\Checks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'chec_name',
        'concept',
        'date',
        'amount',
        'total_amount',
        'type_fund',
        'number_project',
        'bank_id',
        'type_fund_id',
        'iva',
        'rent',
        'net_total',
        'document'
    ];

    protected $timestamp = true;

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
