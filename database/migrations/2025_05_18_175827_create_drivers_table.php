<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id('driver_id');
            $table->string('driver_name', 255);
            $table->text('address')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('region', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('license_number', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('email', 100)->nullable();
            $table->string('latitude', 100)->nullable();
            $table->string('longitude', 100)->nullable();
            $table->string('username', 200)->nullable();
            $table->longText('password')->nullable();
            $table->tinyInteger('manager')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('drivers');
    }
}
