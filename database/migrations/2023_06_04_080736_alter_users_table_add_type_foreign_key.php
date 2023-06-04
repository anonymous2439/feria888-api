<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTableAddTypeForeignKey extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the 'name' column
            $table->dropColumn('name');
            
            // Add the 'type' column as a foreign key
            $table->unsignedBigInteger('type_id');
            $table->foreign('type_id')
                  ->references('id')
                  ->on('user_types')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['type_id']);
            
            // Recreate the 'name' column
            $table->string('name');
        });
    }
}

