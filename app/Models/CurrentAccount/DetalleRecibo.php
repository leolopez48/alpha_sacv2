<?php

namespace App\Models\CurrentAccount;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleRecibo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detalles_recibo';

    public $incrementing = true;

    protected $fillable = [
        'recibo_id',
        'cuenta_id',
        'cantidad',
        'subtotal',
    ];

    protected $data = ['deleted_at'];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $timestamps = true;
}
