<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('number')->unique();
            $table->date('issue_date');

            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('payment_method')->nullable();

            $table->json('items');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->text('terms_and_conditions')->nullable();
            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
