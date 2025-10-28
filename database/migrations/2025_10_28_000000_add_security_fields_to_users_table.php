<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->timestamp('last_failed_attempt')->nullable();
            $table->string('last_failed_ip')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_until')->nullable();
            $table->text('security_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_login_at',
                'last_login_ip', 
                'failed_attempts',
                'last_failed_attempt',
                'last_failed_ip',
                'is_locked',
                'locked_until',
                'security_notes'
            ]);
        });
    }
};