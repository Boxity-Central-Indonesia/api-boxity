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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('website')->nullable();
            $table->string('logo')->nullable(); // Store logo path or data here
            $table->string('address');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->string('country');
            $table->string('industry');
            $table->longText('description', 100000)->nullable();
            $table->timestamps();
        });

        Schema::create('companies_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('responsibilities');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();

            // Buat constraint untuk kolom company_id
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
        Schema::create('companies_branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('phone_number');
            $table->string('email');


            $table->unsignedBigInteger('company_id');
            $table->timestamps();

            // Buat constraint untuk kolom company_id
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        // buat tabel kategori karyawan
        Schema::create('employees_categories', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->index('id');
            $table->string('name');
            $table->longText('description', 100000)->nullable();
            $table->timestamps();
        });

        // Buat tabel karyawan
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('job_title_category_id');
            $table->string('job_title');
            $table->date('date_of_birth');
            $table->string('employment_status');
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->string('country');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone_number');
            $table->text('notes')->nullable();


            $table->unsignedBigInteger('department_id');
            $table->timestamps();

            // Buat constraint untuk kolom department_id
            $table->foreign('department_id')->references('id')->on('companies_departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
        Schema::dropIfExists('companies_departments');
        Schema::dropIfExists('companies_branches');
        Schema::dropIfExists('employees_categories');
        Schema::dropIfExists('employees');
    }
};
