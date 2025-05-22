<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Core schema
            $table->decimal('amount', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->date('date');
            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->foreignId('type_id')->nullable()->constrained('transaction_types')->onDelete('set null');
            $table->enum('type', ['income', 'outcome'])->nullable();
            $table->foreignId('subtype_id')->nullable()->constrained('transaction_subtypes')->onDelete('set null');

            $table->boolean('is_temp')->default(true);
            $table->enum('source', ['csv', 'manual', 'api'])->default('manual');
            $table->string('source_detail')->nullable();
            $table->foreignId('uploaded_file_id')->nullable()->constrained('uploaded_files')->onDelete('set null');
            
            $table->softDeletes();

            // Extra fields from Excel (translated and nullable)
            $table->string('ordering_account')->nullable();             // Auftragskonto
            $table->string('original_descreption')->nullable();             // Auftragskonto
            $table->date('booking_date')->nullable();                   // Buchungstag
            $table->date('value_date')->nullable();                     // Valutadatum
            $table->string('booking_text')->nullable();                 // Buchungstext
            $table->string('purpose')->nullable();                      // Verwendungszweck
            $table->string('creditor_id')->nullable();                  // Glaeubiger ID
            $table->string('mandate_reference')->nullable();            // Mandatsreferenz
            $table->string('customer_reference')->nullable();           // Kundenreferenz
            $table->string('batch_reference')->nullable();              // Sammlerreferenz
            $table->string('original_debit_amount')->nullable();        // Lastschrift Ursprungsbetrag
            $table->string('refund_fee')->nullable();                   // Auslagenersatz Ruecklastschrift
            $table->string('beneficiary')->nullable();                  // Beguenstigter/Zahlungspflichtiger
            $table->string('iban')->nullable();                         // Kontonummer/IBAN
            $table->string('bic')->nullable();                          // BIC (SWIFT-Code)
            $table->string('currency')->nullable();                     // Waehrung
            $table->text('note')->nullable();                           // Info

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
