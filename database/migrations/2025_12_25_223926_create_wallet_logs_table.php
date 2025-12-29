<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * wallet_logs 表用於記錄所有錢包交易的完整審計軌跡
     * 符合 ADR-008 要求
     */
    public function up(): void
    {
        Schema::create('wallet_logs', function (Blueprint $table) {
            $table->id();

            // 關聯的錢包交易
            $table->foreignId('wallet_transaction_id')
                ->constrained('wallet_transactions')
                ->onDelete('cascade');

            // 被操作的用戶
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // 操作人員 (null = 用戶本人操作)
            $table->foreignId('operator_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // 操作者類型
            $table->enum('operator_type', ['user', 'admin', 'system'])
                ->default('user');

            // 交易動作
            $table->string('action', 50); // deposit, payment, refund, adjustment

            // 金額 (cents)
            $table->integer('amount');

            // 餘額變化記錄
            $table->integer('balance_before');
            $table->integer('balance_after');

            // 請求資訊
            $table->string('ip_address', 45)->nullable(); // IPv4/IPv6
            $table->text('user_agent')->nullable();

            // 交易原因/備註
            $table->text('reason')->nullable();

            // 防竄改校驗碼
            $table->string('checksum', 64)->nullable();

            $table->timestamp('created_at')->useCurrent();

            // 索引
            $table->index(['user_id', 'created_at']);
            $table->index('operator_id');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_logs');
    }
};
