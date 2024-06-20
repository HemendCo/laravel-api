<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('acl_services', function (Blueprint $table) {
          $table->unsignedSmallInteger('id', true);
          $table->boolean('activated')->default('1')->unsigned()->nullable()->comment('NULL=Inactivated 1=Activated');
          $table->string('name', 120);
          $table->string('title', 120);
          $table->string('guard_name', 120);
          $table->unsignedSmallInteger('position');
          $table->timestamps();

          $table->unique(['name', 'guard_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acl_services');
    }
};
