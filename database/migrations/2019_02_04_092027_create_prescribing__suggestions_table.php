<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrescribingSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescribing_suggestions', function (Blueprint $table) {
            $table->increments('id');
            $table->date('due_until');
            $table->integer('reason_id')->nullable();
            $table->string('reason_suggestion')->nullable();
            $table->boolean('finished')->default($value = false);
            $table->boolean('approved')->default($value = false);
            $table->string('title');
            $table->text('description');
            $table->integer('author_id');
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
        Schema::dropIfExists('prescribing_suggestions');
    }
}
