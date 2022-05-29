<?php

namespace App\Models\Checks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name_bank',
        'account_number',
    ];

    protected $timestamp = true;
}
