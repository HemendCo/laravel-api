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
      $table->unsignedBigInteger('created_by')->nullable()->index('created_by');
      $table->enum('gender', ['M', 'F'])->nullable()->comment('M=Male F=Female');
      $table->string('first_name', 70)->nullable();
      $table->string('last_name', 70)->nullable();
      $table->string('national_code', 10)->nullable()->index('national_code');
      $table->string('mobile', 11)->nullable()->index('mobile');
      $table->timestamp('mobile_verified_at')->nullable();
      $table->string('email')->nullable()->index('email');
      $table->timestamp('email_verified_at')->nullable();
      $table->string('username')->nullable()->index('username');
      $table->string('password')->nullable();
      $table->rememberToken();
      $table->timestamps();

      $table->unique(['national_code', 'not_deleted'], 'nationalCode_notDeleted_unique');
      $table->unique(['username', 'not_deleted'], 'username_notDeleted_unique');
      $table->unique(['mobile', 'not_deleted'], 'mobile_notDeleted_unique');
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
