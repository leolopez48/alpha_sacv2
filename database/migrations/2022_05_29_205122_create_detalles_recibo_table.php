<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallesReciboTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_recibo', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('recibo_id')->index('fk_detalles_recibo_recibo1_idx');
            $table->integer('cuenta_id')->index('fk_detalles_recibo_cuenta1_idx')->nullable();
            $table->double('cantidad')->nullable();
            $table->double('subtotal')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalles_recibo');
    }
}
