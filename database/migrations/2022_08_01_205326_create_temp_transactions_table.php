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
        Schema::create('temp_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('origin');
            $table->string('destination');
            $table->foreign('origin')->references('card_number')
                ->on('cards')->onDelete('cascade');
            $table->foreign('destination')->references('card_number')
                ->on('cards')->onDelete('cascade');
            $table->bigInteger('amount');
            $table->tinyInteger('status')->default(0)->comment('0 : waiting , 1 : confirmed , 2 : failed');
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
        Schema::dropIfExists('temp_transactions');
    }
};
