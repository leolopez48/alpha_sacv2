<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checks', function (Blueprint $table) {
            $table->id();
            // $table->string('code');
            $table->string('check_number');
            // $table->string('check_name');
            $table->string('concept');
            $table->string('date');
            $table->double('amount');
            $table->string('total_letters');
            $table->string('movement');
            $table->string('iva');
            $table->string('rent');
            $table->string('net_total');
            $table->string('document');
            $table->string('number_project')->nullable();
            $table->foreignId('type_fund_id')->constrained('documents');
            // $table->foreignId('bank_id')->constrained('banks');
            $table->foreignId('supplier_id')->constrained('suppliers');
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
        Schema::dropIfExists('checks');
    }
}
