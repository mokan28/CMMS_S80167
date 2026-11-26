<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vessels', function (Blueprint $t) { $t->id(); $t->string('name'); $t->text('description')->nullable(); $t->timestamps(); });
        Schema::create('assets', function (Blueprint $t) {
            $t->id(); $t->foreignId('vessel_id')->constrained()->cascadeOnDelete();
            $t->string('code')->unique(); $t->string('name'); $t->string('subsystem'); $t->string('location'); $t->string('criticality'); $t->string('service_status')->default('in_service'); $t->boolean('has_meter')->default(false); $t->timestamps();
        });
        Schema::create('users', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('email')->unique(); $t->string('password'); $t->string('role'); $t->rememberToken(); $t->timestamps();
        });
        Schema::create('maintenance_plans', function (Blueprint $t) {
            $t->id(); $t->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $t->string('title'); $t->enum('trigger_type',['calendar','hours','dual']);
            $t->unsignedInteger('interval_days')->nullable(); $t->unsignedInteger('interval_hours')->nullable();
            $t->date('last_completion_date')->nullable(); $t->unsignedInteger('last_completion_hours')->nullable();
            $t->string('source_label')->default('Synthetic teaching scenario'); $t->timestamps();
            $t->unique(['asset_id','title']);
        });
        Schema::create('work_orders', function (Blueprint $t) {
            $t->id(); $t->foreignId('plan_id')->constrained('maintenance_plans')->cascadeOnDelete();
            $t->enum('state',['open','in_progress','submitted','closed'])->default('open');
            $t->foreignId('assignee_id')->nullable()->constrained('users'); $t->foreignId('opened_by')->constrained('users');
            $t->date('actual_completion_date')->nullable(); $t->unsignedInteger('actual_completion_hours')->nullable();
            $t->text('completion_evidence')->nullable(); $t->timestamp('submitted_at')->nullable();
            $t->timestamp('closed_at')->nullable(); $t->foreignId('closed_by')->nullable()->constrained('users');
            $t->text('return_reason')->nullable(); $t->timestamps();
        });
        // one active work order per plan
        Schema::create('spare_items', function (Blueprint $t) {
            $t->id(); $t->string('code')->unique(); $t->string('description'); $t->string('unit'); $t->integer('quantity')->default(0); $t->integer('reorder_level')->default(0); $t->timestamps();
        });
        Schema::create('spare_movements', function (Blueprint $t) {
            $t->id(); $t->foreignId('spare_item_id')->constrained()->cascadeOnDelete();
            $t->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $t->integer('quantity'); $t->foreignId('actor_id')->constrained('users'); $t->text('note')->nullable(); $t->timestamps();
        });
        Schema::create('audit_events', function (Blueprint $t) {
            $t->id(); $t->foreignId('actor_id')->nullable()->constrained('users');
            $t->string('action'); $t->string('record_type'); $t->unsignedBigInteger('record_id'); $t->text('detail')->nullable(); $t->timestamps();
        });
        Schema::create('sessions', function (Blueprint $t) { $t->string('id')->primary(); $t->foreignId('user_id')->nullable(); $t->string('ip_address',45)->nullable(); $t->text('user_agent')->nullable(); $t->longText('payload'); $t->integer('last_activity')->index(); });
    }
    public function down(): void
    {
        foreach (['audit_events','spare_movements','spare_items','work_orders','maintenance_plans','users','assets','vessels','sessions'] as $t) Schema::dropIfExists($t);
    }
};
