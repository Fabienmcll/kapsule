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
        Schema::table('kapsule_user', function (Blueprint $table) {
            //Ajout d'un champ pour accepter ou non une invitation à rejoindre une kapsule
            $table->boolean('accepted')->default(false)->after('user_id');
            $table->boolean('is_pending')->default(true)->after('accepted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kapsule_user', function (Blueprint $table) {
            //
            $table->dropColumn('accepted');
            $table->dropColumn('is_pending');
        });
    }
};
