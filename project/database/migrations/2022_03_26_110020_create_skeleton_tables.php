<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkeletonTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('token')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->index(['first_name']);
            $table->index(['email']);
        });

        Schema::create('companies', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('phone')->unique();
            $table->string('description');
            $table->timestamps();

            $table->index(['title']);
            $table->index(['phone']);
        });

        Schema::create('user_company', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('user_company');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('users');
    }
}

