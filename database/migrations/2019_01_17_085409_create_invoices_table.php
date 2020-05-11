<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reason_id');
            $table->integer('author_id');
            $table->boolean('imported_prescribing')->default($value = false);
            $table->text('annotation')->nullable();
            $table->boolean('approved')->default($value = false);
            $table->boolean('saved')->default($value = false);
            $table->float('total_amount', 8, 2);
            $table->date('date');
            $table->date('due_until');
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
        Schema::dropIfExists('invoices');
    }
}
