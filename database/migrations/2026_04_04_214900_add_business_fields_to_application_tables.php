<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('slug')->nullable()->after('title');
            $table->boolean('is_premium')->default(false)->after('thumbnail');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(1)->after('video_url');
            $table->boolean('is_preview')->default(false)->after('sort_order');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway')->default('razorpay')->after('status');
            $table->string('gateway_order_id')->nullable()->after('gateway');
            $table->string('gateway_payment_id')->nullable()->after('gateway_order_id');
            $table->timestamp('verified_at')->nullable()->after('gateway_payment_id');
            $table->json('meta')->nullable()->after('verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['gateway', 'gateway_order_id', 'gateway_payment_id', 'verified_at', 'meta']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'is_preview']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn(['slug', 'is_premium']);
        });

        Schema::dropIfExists('categories');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
