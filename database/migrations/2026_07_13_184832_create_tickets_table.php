<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('help_article_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_email');
            $table->string('channel'); // airbnb, booking, direct, email
            $table->text('message');
            $table->string('category')->default('other'); // access, wifi, billing, cleaning, noise, other
            $table->string('priority')->default('normal'); // urgent, high, normal, low
            $table->string('status')->default('new'); // new, triaged, investigating, waiting, resolved
            $table->unsignedTinyInteger('confidence')->default(0); // 0-100
            $table->boolean('needs_escalation')->default(false);
            $table->text('draft_reply')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
