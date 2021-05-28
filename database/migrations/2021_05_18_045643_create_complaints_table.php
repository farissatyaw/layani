<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->text('body');
            $table->string('location');
            $table->enum('status', ['unfinished', 'inprogress', 'finished']);
            $table->dateTime('tweettimestamp');

            $table->string('photo')->nullable();
            $table->text('note')->nullable();
            $table->boolean('isUserGenerated')->default(0);
            $table->dateTime('datetaken')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('complaints');
    }
}
