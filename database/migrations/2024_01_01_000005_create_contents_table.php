<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type')->default('block'); // block, section, widget
            $table->longText('content')->nullable();
            $table->json('settings')->nullable();
            $table->string('location')->nullable(); // where to display
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['type', 'is_active']);
            $table->index(['location', 'is_active', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contents');
    }
};