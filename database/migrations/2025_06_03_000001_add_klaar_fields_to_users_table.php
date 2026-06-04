<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('company_name')->nullable()->after('phone');
            $table->string('kvk_number', 20)->nullable()->after('company_name');
            $table->string('btw_number', 30)->nullable()->after('kvk_number');
            $table->string('address_street')->nullable()->after('btw_number');
            $table->string('address_housenumber', 10)->nullable()->after('address_street');
            $table->string('address_postcode', 10)->nullable()->after('address_housenumber');
            $table->string('address_city')->nullable()->after('address_postcode');
            $table->string('address_country', 2)->default('NL')->after('address_city');
            $table->string('plan')->default('free')->after('address_country');
            $table->timestamp('trial_ends_at')->nullable()->after('plan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'company_name', 'kvk_number', 'btw_number',
                'address_street', 'address_housenumber', 'address_postcode',
                'address_city', 'address_country', 'plan', 'trial_ends_at',
            ]);
        });
    }
};
