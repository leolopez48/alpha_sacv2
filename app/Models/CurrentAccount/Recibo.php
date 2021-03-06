<?php

namespace App\Models\CurrentAccount;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recibo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recibo';

    public $incrementing = true;

    protected $fillable = [
        'fecha_registro',
        'dui',
        'nombres',
        'apellidos',
        'concepto',
        'total',
        'direccion'
    ];

    protected $data = ['deleted_at'];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $timestamps = true;
}
