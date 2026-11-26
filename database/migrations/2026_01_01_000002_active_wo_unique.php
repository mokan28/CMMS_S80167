<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void {
        DB::statement("CREATE UNIQUE INDEX work_orders_one_active_per_plan ON work_orders (plan_id) WHERE state IN ('open','in_progress','submitted')");
    }
    public function down(): void { DB::statement("DROP INDEX IF EXISTS work_orders_one_active_per_plan"); }
};
