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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['owner', 'unit_bisnis'])->default('unit_bisnis')->after('password');
            $table->foreignId('unit_bisnis_id')->nullable()->constrained('unit_bisnis')->onDelete('set null')->after('role');
            $table->boolean('is_active')->default(true)->after('unit_bisnis_id');
            $table->string('photo', 255)->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unit_bisnis_id']);
            $table->dropColumn(['role', 'unit_bisnis_id', 'is_active', 'photo']);
        });
    }
};
