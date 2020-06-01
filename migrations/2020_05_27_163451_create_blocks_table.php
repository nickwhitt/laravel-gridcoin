<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->char('hash', 64)->index();
            $table->float('mint');
            $table->timestamp('time')->index();
            $table->float('difficulty');
            $table->char('cpid', 32)->nullable()->index();
            $table->float('interest')->nullable();
            $table->float('researchsubsidy')->nullable();
            $table->char('previousblockhash', 64)->nullable();
            $table->char('nextblockhash', 64)->nullable();
            $table->char('lastporblockhash', 64)->nullable();
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
        Schema::dropIfExists('blocks');
    }
}
