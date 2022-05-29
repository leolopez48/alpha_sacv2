<?php

namespace App\Models\Checks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'code',
        'name_document',
    ];

    protected $timestamp = true;
}
