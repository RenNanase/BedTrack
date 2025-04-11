<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bassinets', function (Blueprint $table) {
            if (!Schema::hasColumn('bassinets', 'mother_name')) {
                $table->string('mother_name')->nullable();
            }
            if (!Schema::hasColumn('bassinets', 'mother_mrn')) {
                $table->string('mother_mrn')->nullable();
            }
            if (!Schema::hasColumn('bassinets', 'occupied_at')) {
                $table->timestamp('occupied_at')->nullable();
            }
            if (!Schema::hasColumn('bassinets', 'status_changed_at')) {
                $table->timestamp('status_changed_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('bassinets', function (Blueprint $table) {
            $table->dropColumn(['mother_name', 'mother_mrn', 'occupied_at', 'status_changed_at']);
        });
    }
}; 