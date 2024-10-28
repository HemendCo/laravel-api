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
    Schema::create(env('API_DB_QUEUE_TRACKERS_TABLE', 'system_jobs_trackers'), function (Blueprint $table) {
      $table->id();
      $table->string('job_id')->index('job_id')->nullable();
      $table->string('type')->index('type');
      $table->string('queue')->index('queue')->nullable();
      $table->unsignedTinyInteger('attempts')->default(0);
      $table->unsignedInteger('progress_now')->default(0);
      $table->unsignedInteger('progress_max')->default(0);
      $table->string('status', 16)->default(\Hemend\Api\TypeHint\JobTrackerStatus::QUEUED->value)->index();
      $table->longText('input')->nullable();
      $table->longText('output')->nullable();
      $table->timestamp('executed_at')->nullable()->comment('When the command should be executed');
      $table->timestamp('started_at')->nullable()->comment('When the command was started');
      $table->timestamp('finished_at')->nullable()->comment('When the command was finished');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists(env('API_DB_QUEUE_TRACKERS_TABLE', 'system_jobs_trackers'));
  }
};
