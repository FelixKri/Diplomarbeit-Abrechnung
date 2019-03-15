<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Faker\Factory as Faker;

class CreateTosUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tos_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id');
            $table->string('username');
            $table->string('username_canonical');
            $table->string('email');
            $table->string('email_canonical');
            $table->boolean('enabled')->default($value = true);
            $table->string('salt');
            $table->string('password');
            $table->dateTime('last_login');
            $table->boolean('locked')->default($value = false);
            $table->boolean('expired')->default($value = false);
            $table->dateTime('expires_at');
            $table->string('confirmation_token');
            $table->dateTime('password_requested_at');
            $table->string('roles');
            $table->boolean('credentials_expired')->default($value = false);
            $table->dateTime('credentials_expire_at');
            $table->string('last_name');
            $table->string('first_name');
            $table->string('sokr_key');
            $table->string('iban');
            $table->string('bic');
            $table->date('birthday');
            $table->dateTIme('checkout_date');
            $table->timestamps();
        });

        $faker = Faker::create();
    	foreach (range(1,100) as $index) {
	        DB::table('tos_users')->insert([
	            'group_id' => rand(1, 10),
                'username' => $faker->name,
                'username_canonical' => $faker->name,
                'email' => $faker->email,
                'email_canonical' => $faker->email,
                'salt' => "adsf&263fasf",
                'password' => bcrypt('secret'),
                'last_login' => $faker->dateTime(),
                'expires_at' => $faker->dateTime(),
                'confirmation_token' => "sdaf7&A/6sa87d6fzzs7fa67(&/(6szf",
                'password_requested_at' => $faker->dateTime(),
                'roles' => 'student | WB',
                'credentials_expire_at' => $faker->dateTime(),
                'last_name' => $faker->lastName,
                'first_name' => $faker->firstName,
                'sokr_key' => "asdfasdfasdf",
                'iban' => 'AT8112318838441823',
                'bic' => '123ADsda78',
                'birthday' => $faker->date(),
                'checkout_date' => $faker->date()
	        ]);
	    }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tos_users');
    }
}
