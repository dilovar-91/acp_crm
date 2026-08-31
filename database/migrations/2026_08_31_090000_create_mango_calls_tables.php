<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMangoCallsTables extends Migration
{
    public function up()
    {
        Schema::create('mango_calls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mango_account_id');
            $table->string('entry_id', 128);
            $table->unsignedBigInteger('showroom_id')->nullable()->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->unsignedBigInteger('site_id')->nullable()->index();
            $table->unsignedBigInteger('operator_id')->nullable()->index();
            $table->unsignedTinyInteger('direction')->nullable()->index();
            $table->string('client_phone', 32)->nullable()->index();
            $table->string('line_number', 255)->nullable();
            $table->string('operator_extension', 64)->nullable();
            $table->string('status', 32)->default('new')->index();
            $table->unsignedTinyInteger('entry_result')->nullable();
            $table->integer('disconnect_reason')->nullable();
            $table->unsignedBigInteger('create_time')->nullable();
            $table->unsignedBigInteger('talk_time')->nullable();
            $table->unsignedBigInteger('end_time')->nullable();
            $table->json('call_sequences')->nullable();
            $table->boolean('popup_sent')->default(false);
            $table->boolean('summary_processed')->default(false);
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['mango_account_id', 'entry_id'], 'mango_calls_account_entry_unique');
        });

        Schema::create('mango_call_recordings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mango_call_id')->nullable()->index();
            $table->unsignedBigInteger('mango_account_id');
            $table->string('entry_id', 128)->index();
            $table->string('recording_id', 128)->unique();
            $table->bigInteger('user_id')->nullable();
            $table->unsignedBigInteger('recorded_at')->nullable();
            $table->timestamp('attached_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(
                ['mango_account_id', 'entry_id'],
                'mango_recordings_account_entry_index'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('mango_call_recordings');
        Schema::dropIfExists('mango_calls');
    }
}
