<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHasInvoicePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
    */
    public function up()
    {
        Schema::create('user_has__invoice__positions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('invoice_position_id');
            $table->string('comment')->nullable($value = true);
            $table->float('amount', 8, 2);
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
        Schema::dropIfExists('user_has__invoice__positions');
    }
}
