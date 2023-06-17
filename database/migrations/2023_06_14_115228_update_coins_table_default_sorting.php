<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCoinsTableDefaultSorting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coins', function (Blueprint $table) {
            // Drop the existing index
            $table->dropIndex('coins_created_index');

            // Add the new index with default sorting on 'id' in descending order
            $table->index('id', 'coins_default_index')->desc();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coins', function (Blueprint $table) {
            // Drop the new index
            $table->dropIndex('coins_default_index');

            // Add the previous index with sorting on 'created_at' and 'created_by' in ascending order
            $table->index(['created_at', 'created_by'], 'coins_created_index');
        });
    }
}

