<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMesinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mesins', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->boolean('is_online')->default(false);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_on')->default(false);
            $table->integer('batas_on')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mesins');
    }
}
