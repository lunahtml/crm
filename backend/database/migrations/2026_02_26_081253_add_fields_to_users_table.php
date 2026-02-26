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
            $table->string('position')->nullable(); // Должность
            $table->string('role')->default('employee'); // Роль (admin, project_owner, backend, designer)
            $table->decimal('hourly_rate', 8, 2)->nullable(); // Почасовая ставка
            $table->decimal('salary', 10, 2)->nullable(); // Месячная зарплата
            $table->date('hired_at')->nullable(); // Дата найма
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['position', 'role', 'hourly_rate', 'salary', 'hired_at']);
        });
    }
};
