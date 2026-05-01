<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassedOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('passed_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // Reference to the orders table
            $table->timestamps();

            // Add a foreign key constraint to ensure data integrity
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('passed_orders');
    }
}
