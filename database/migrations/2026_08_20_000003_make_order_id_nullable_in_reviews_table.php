<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE reviews DROP FOREIGN KEY reviews_order_id_foreign');
        DB::statement('ALTER TABLE reviews MODIFY COLUMN order_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE reviews MODIFY COLUMN order_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE reviews ADD CONSTRAINT reviews_order_id_foreign FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
    }
};
