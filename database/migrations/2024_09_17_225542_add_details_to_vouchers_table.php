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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('series')->nullable()->after('xml_content');
            $table->string('number')->nullable()->after('series');
            $table->string('voucher_type')->nullable()->after('number');
            $table->string('currency', 3)->nullable()->after('voucher_type'); // ISO 4217
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['series', 'number', 'voucher_type', 'currency']);
        });
    }
};
