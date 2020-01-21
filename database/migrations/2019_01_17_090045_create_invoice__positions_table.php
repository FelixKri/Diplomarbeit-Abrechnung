<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Place for paste
        
        insert into fos_user (username, username_canonical, email, email_canonical, 
enabled, salt, password, locked, expired, roles, credentials_expired) 
values ("TestLehrer1", "TestLehrer1", "testlehrer@gmail.com", "testlehrer@gmail.com", 
true, "", "$2y$12$k45Uz9i0m7j/mPiE9uaI4OJ6Xcn/kvMPBlxj/yL4ZAEp6kwX6SR22", false, 
false, 'a:1:{i:0;s:10:"ROLE_ADMIN";}', false);

        */


        Schema::create('invoice_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->float('total_amount', 8, 2);
            $table->integer('invoice_id');
            $table->boolean('paid_by_teacher');
            $table->string('iban')->nullable();
            $table->integer('document_number');
            $table->string('annotation');
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
        Schema::dropIfExists('invoice_positions');
    }
}
