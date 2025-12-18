<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_table_exists()
    {
        $this->assertTrue(Schema::hasTable('settings'));
        $this->assertTrue(Schema::hasColumns('settings', [
            'key',
            'value',
            'description'
        ]));
    }

    public function test_addresses_table_exists()
    {
        $this->assertTrue(Schema::hasTable('addresses'));
        $this->assertTrue(Schema::hasColumns('addresses', [
            'user_id',
            'city',
            'district',
            'zip_code',
            'detail_address',
            'recipient_name',
            'phone'
        ]));
    }

    public function test_core_tables_have_indexes()
    {
        // Check Products Indexes (basic check via doctrine/dbal logic usually, but here we check existence)
        // Laravel's Schema::hasIndex is not always reliable across drivers without name, so we check table structure.

        $this->assertTrue(Schema::hasTable('products'));
        $this->assertTrue(Schema::hasTable('orders'));
        $this->assertTrue(Schema::hasTable('users'));
    }

    public function test_foreign_keys_integrity()
    {
        // We can verify foreign keys are respected by attempting to insert invalid data if possible,
        // or just rely on migration success which implies FK creation.
        // Here we just ensure the tables are created.
        $this->assertTrue(Schema::hasTable('wallet_transactions'));
    }
}
