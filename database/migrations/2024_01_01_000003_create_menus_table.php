<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->json('items');
            $table->boolean('is_active')->default(true);
            $table->integer('max_depth')->default(2);
            $table->timestamps();
            
            $table->unique(['name', 'location']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('menus');
    }
};