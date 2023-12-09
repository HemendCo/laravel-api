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
            $table->boolean('not_deleted')->default('1')->unsigned()->nullable()->comment('NULL=Deleted 1=Not Deleted');
            $table->boolean('blocked')->unsigned()->nullable()->comment('NULL=UnBlocked 1=Blocked');
            $table->boolean('suspended')->unsigned()->nullable()->comment('NULL=UnSuspended 1=Suspended');
            $table->boolean('activated')->unsigned()->nullable()->comment('NULL=Inactivated 1=Activated');
            $table->string('first_name', 70)->nullable();
            $table->string('last_name', 70)->nullable();
            $table->enum('gender', ['M', 'F'])->nullable()->comment('M=Male F=Female');
            $table->string('mobile', 11)->nullable()->index('mobile');
            $table->unsignedBigInteger('created_by')->nullable()->index('created_by');
            $table->timestamps();

            $table->unique(['mobile', 'not_deleted'], 'mobile_not_deleted_unique');
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
