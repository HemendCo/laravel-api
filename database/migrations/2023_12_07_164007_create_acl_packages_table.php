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
        Schema::create('acl_packages', function (Blueprint $table) {
          $table->unsignedSmallInteger('id', true);
          $table->boolean('activated')->default('1')->unsigned()->comment('NULL=Inactivated 1=Activated');
          $table->unsignedSmallInteger('service_id')->index('service_id');
          $table->string('name', 120);
          $table->longText('title');
          $table->string('guard_name', 120);
          $table->unsignedSmallInteger('position');
          $table->timestamps();

          $table->unique(['service_id', 'name', 'guard_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acl_packages');
    }
};
