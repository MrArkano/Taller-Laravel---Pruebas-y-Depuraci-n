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
        // 1. Asegurar tabla Categories
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        } elseif (!Schema::hasColumn('categories', 'description')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->text('description')->nullable()->after('slug');
            });
        }

        // 2. Tabla Publishers (Editoriales)
        if (!Schema::hasTable('publishers')) {
            Schema::create('publishers', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('slug')->unique();
                $table->string('country')->default('Japón');
                $table->text('description')->nullable();
                $table->string('website')->nullable();
                $table->timestamps();
            });
        }

        // 3. Tabla Mangas
        if (!Schema::hasTable('mangas')) {
            Schema::create('mangas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->foreignId('publisher_id')->constrained('publishers')->cascadeOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('author');
                $table->text('synopsis');
                $table->decimal('price', 10, 2);
                $table->unsignedInteger('stock')->default(0);
                $table->string('cover_image')->nullable();
                $table->string('isbn', 30)->nullable();
                $table->unsignedSmallInteger('pages')->nullable();
                $table->string('format')->default('Tankobon (11.5x17 cm)');
                $table->boolean('is_featured')->default(false);
                $table->timestamps();

                $table->index(['title', 'author']);
            });
        }

        // 4. Tabla Orders
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
                $table->string('customer_name');
                $table->string('customer_email');
                $table->string('customer_phone')->nullable();
                $table->string('shipping_address');
                $table->string('city');
                $table->string('postal_code')->nullable();
                $table->decimal('subtotal', 10, 2);
                $table->decimal('tax', 10, 2)->default(0);
                $table->decimal('shipping_cost', 10, 2)->default(0);
                $table->decimal('total', 10, 2);
                $table->string('status')->default('completed');
                $table->string('payment_method')->default('card');
                $table->string('payment_status')->default('paid');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 5. Tabla Order Items
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('manga_id')->constrained('mangas')->restrictOnDelete();
                $table->unsignedInteger('quantity')->default(1);
                $table->decimal('unit_price', 10, 2);
                $table->decimal('total_price', 10, 2);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('mangas');
        Schema::dropIfExists('publishers');
    }
};
