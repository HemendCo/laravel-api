<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0)->comment('-1=Deleted 0=Inactive 1=Active 2=Suspend 3=Block');
            $table->string('first_name', 70)->nullable();
            $table->string('last_name', 70)->nullable();
            $table->enum('gender', ['M', 'F'])->nullable()->comment('M=Male F=Female');
            $table->string('mobile', 11)->unique('mobile');
            $table->unsignedBigInteger('created_by')->default(0)->index('created_by');
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
        Schema::dropIfExists('users');
    }
}
