<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->change();
            
            $table->foreignId('destination_account_id')
                ->nullable()
                ->after('account_id')
                ->constrained('accounts')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable(false)->change();
            $table->dropForeign(['destination_account_id']);
            $table->dropColumn('destination_account_id');
        });
    }
};