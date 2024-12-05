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
    Schema::create('acl_service_has_roles', function (Blueprint $table) {
      $table->unsignedSmallInteger('service_id')->comment('شناسه سرویس');
      $table->foreignId('role_id')->comment('شناسه وظیفه')->constrained('acl_roles')->onDelete('cascade');

      $table->foreign('service_id')->references('id')->on('acl_services')->onDelete('cascade');
      $table->unique(['service_id', 'role_id'], 'serviceId_roleId_unique');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('acl_service_has_roles');
  }
};
