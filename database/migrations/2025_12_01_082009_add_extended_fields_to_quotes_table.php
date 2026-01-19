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
        Schema::table('quotes', function (Blueprint $table) {
            $table->foreignId('supplier_invitation_id')->nullable()->after('supplier_id')->constrained('supplier_invitations')->nullOnDelete();
            $table->decimal('total_amount', 10, 2)->nullable()->after('total_price');
            $table->string('currency', 3)->default('USD')->after('total_amount');
            $table->timestamp('valid_until')->nullable()->after('currency');
            $table->text('terms_conditions')->nullable()->after('notes');
            $table->timestamp('submitted_at')->nullable()->after('status');

            $table->index('supplier_invitation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['supplier_invitation_id']);
            $table->dropColumn([
                'supplier_invitation_id',
                'total_amount',
                'currency',
                'valid_until',
                'terms_conditions',
                'submitted_at',
            ]);
        });
    }
};
