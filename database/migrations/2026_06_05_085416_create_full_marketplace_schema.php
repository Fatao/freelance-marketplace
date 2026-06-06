<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

      // CACHE
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });




        // PASSWORD RESET TOKENS
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // SESSIONS
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // USERS
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['freelancer', 'client', 'moderator', 'admin'])->default('freelancer');
            $table->boolean('is_blocked')->default(false);
            $table->string('block_reason')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // CATEGORIES
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // SKILLS
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // FREELANCER PROFILES
        Schema::create('freelancer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('display_name')->nullable();
            $table->string('specialization')->nullable();
            $table->text('experience')->nullable();
            $table->text('portfolio')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->string('phone')->nullable();
            $table->string('telegram')->nullable();
            $table->string('website')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // CLIENT PROFILES
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('company_name')->nullable();
            $table->text('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('telegram')->nullable();
            $table->string('website')->nullable();
            $table->boolean('contact_verified')->default(false);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->timestamps();
        });

        // FREELANCER SKILLS
        Schema::create('freelancer_skills', function (Blueprint $table) {
            $table->foreignId('freelancer_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->primary(['freelancer_profile_id', 'skill_id']);
        });

        // ORDERS
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->enum('payment_format', ['fixed', 'hourly', 'negotiable'])->default('fixed');
            $table->date('deadline')->nullable();
            $table->json('files')->nullable();
            $table->string('links')->nullable();
            $table->enum('status', [
                'draft', 'on_moderation', 'published',
                'in_progress', 'completed', 'cancelled', 'rejected'
            ])->default('draft');
            $table->string('rejection_reason')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ORDER SKILLS
        Schema::create('order_skills', function (Blueprint $table) {
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->primary(['order_id', 'skill_id']);
        });

        // ORDER STATUS HISTORY
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users');
            $table->string('old_status');
            $table->string('new_status');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // ORDER APPLICATIONS
        Schema::create('order_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
            $table->text('cover_letter');
            $table->decimal('proposed_price', 12, 2)->nullable();
            $table->integer('proposed_days')->nullable();
            $table->enum('status', ['sent', 'viewed', 'accepted', 'rejected', 'withdrawn'])->default('sent');
            $table->timestamps();
            $table->unique(['order_id', 'freelancer_id']);
        });

        // ORDER WORK
        Schema::create('order_work', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained('users');
            $table->enum('status', ['in_progress', 'on_review', 'needs_revision', 'completed', 'cancelled'])
                ->default('in_progress');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // ORDER MESSAGES
        Schema::create('order_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users');
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // REVIEWS
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users');
            $table->foreignId('recipient_id')->constrained('users');
            $table->tinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['order_id', 'author_id']);
        });

        // SAVED SEARCHES
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('categories')->nullable();
            $table->string('keywords')->nullable();
            $table->json('skills')->nullable();
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
        });

        // CRAWLER SOURCES
        Schema::create('crawler_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('base_url');
            $table->json('crawl_rules');
            $table->json('extract_rules');
            $table->integer('frequency_minutes')->default(360);
            $table->enum('status', ['active', 'disabled', 'error'])->default('active');
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });

        // CRAWLER LOGS
        Schema::create('crawler_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crawler_source_id')->constrained()->cascadeOnDelete();
            $table->foreignId('started_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('trigger', ['manual', 'scheduled']);
            $table->integer('found')->default(0);
            $table->integer('created')->default(0);
            $table->integer('updated')->default(0);
            $table->integer('archived')->default(0);
            $table->integer('errors')->default(0);
            $table->text('error_details')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        // EXTERNAL ORDERS
        Schema::create('external_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crawler_source_id')->constrained();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('skills')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->string('source_url')->unique();
            $table->enum('status', ['new', 'active', 'archived', 'error'])->default('new');
            $table->timestamp('discovered_at');
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();
        });

        // COMPLAINTS
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users');
            $table->morphs('complainable');
            $table->text('reason');
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution')->nullable();
            $table->timestamps();
        });




        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');




        // NOTIFICATIONS
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('external_orders');
        Schema::dropIfExists('crawler_logs');
        Schema::dropIfExists('crawler_sources');
        Schema::dropIfExists('saved_searches');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('order_messages');
        Schema::dropIfExists('order_work');
        Schema::dropIfExists('order_applications');
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_skills');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('freelancer_skills');
        Schema::dropIfExists('client_profiles');
        Schema::dropIfExists('freelancer_profiles');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
    }
};