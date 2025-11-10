<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('custom_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->string('controller');
            $table->string('method');
            $table->json('middleware')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->unique('path');
            $table->index(['active', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_routes');
    }
};