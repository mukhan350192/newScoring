<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdl', function (Blueprint $table) {
            $table->id();
            $table->integer('pdl_id');
            $table->string('iin');
            $table->string('model_type')->nullable();
            $table->string('model_type_version')->nullable();
            $table->string('default_probability')->nullable();
            $table->string('default_probability_range')->nullable();
            $table->string('risk_grade')->nullable();
            $table->integer('score')->nullable();
            $table->string('reason_code')->nullable();
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
        Schema::dropIfExists('pdl');
    }
};
