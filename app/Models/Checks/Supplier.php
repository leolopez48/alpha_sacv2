<?php

namespace App\Models\Checks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name_supplier',
        'dui',
        'nit',
        'address',
    ];

    protected $timestamp = true;
}
