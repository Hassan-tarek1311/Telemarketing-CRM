<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('source')->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'lost', 'converted'])->default('new');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->decimal('value', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('follow_up_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // relation to users (لو جدول users موجود)
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
