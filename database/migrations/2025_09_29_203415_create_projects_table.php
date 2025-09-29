<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 30.09.25
 * Time: 00:34
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('projects', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
