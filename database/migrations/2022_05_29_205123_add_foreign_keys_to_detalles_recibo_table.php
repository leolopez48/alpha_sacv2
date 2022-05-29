<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDetallesReciboTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detalles_recibo', function (Blueprint $table) {
            $table->foreign(['cuenta_id'], 'fk_detalles_recibo_cuenta1')->references(['id'])->on('cuenta')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['recibo_id'], 'fk_detalles_recibo_recibo1')->references(['id'])->on('recibo')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalles_recibo', function (Blueprint $table) {
            $table->dropForeign('fk_detalles_recibo_cuenta1');
            $table->dropForeign('fk_detalles_recibo_recibo1');
        });
    }
}
