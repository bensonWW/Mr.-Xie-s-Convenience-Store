<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_critical_tables_have_indexes()
    {
        $indexes = Schema::getIndexes('products');
        // dump($indexes); // Debugging

        // Schema::getIndexes returns an array of arrays with 'name' key.
        $indexNames = collect($indexes)->pluck('name');

        // Assert Indexes exist on Products
        $this->assertTrue(
            $indexNames->contains('products_category_index'),
            'Products table missing index on category. Found: ' . $indexNames->implode(', ')
        );

        // Assert Indexes exist on Orders
        $orderIndexes = collect(Schema::getIndexes('orders'))->pluck('name');
        $this->assertTrue(
            $orderIndexes->contains('orders_status_index'),
            'Orders table missing index on status. Found: ' . $orderIndexes->implode(', ')
        );
        $this->assertTrue(
            $orderIndexes->contains('orders_created_at_index'),
            'Orders table missing index on created_at. Found: ' . $orderIndexes->implode(', ')
        );

        // Assert Indexes exist on Wallet Transactions
        $walletIndexes = collect(Schema::getIndexes('wallet_transactions'))->pluck('name');

        $this->assertTrue(
            $walletIndexes->contains('wallet_transactions_type_index'),
            'WalletTransactions table missing index on type. Found: ' . $walletIndexes->implode(', ')
        );
        $this->assertTrue(
            $walletIndexes->contains('wallet_transactions_created_at_index'),
            'WalletTransactions table missing index on created_at. Found: ' . $walletIndexes->implode(', ')
        );
    }

    public function test_soft_deletes_are_enabled()
    {
        $this->assertTrue(Schema::hasColumn('users', 'deleted_at'), 'Users table missing deleted_at');
        $this->assertTrue(Schema::hasColumn('products', 'deleted_at'), 'Products table missing deleted_at');
        $this->assertTrue(Schema::hasColumn('orders', 'deleted_at'), 'Orders table missing deleted_at');
    }

    public function test_audit_foreign_keys()
    {
        // Just sampling one critical FK
        $this->assertTrue(Schema::hasColumn('orders', 'user_id'), 'Orders table missing user_id');
    }
}
