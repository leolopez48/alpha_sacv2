<?php

namespace App\Models\CurrentAccount;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cuenta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cuenta';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $timestamps = true;
}
