<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void {
Schema::create('hr_employee_profiles', function (Blueprint $table) {
$table->id();
// relasi longgar ke users & struktur organisasi
$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
$table->foreignId('jabatan_id')->nullable()->constrained('jabatans')->nullOnDelete();
$table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
$table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();


// identitas inti
$table->string('nik', 50)->nullable()->index();
$table->string('nama_lengkap');
$table->string('email')->nullable();
$table->string('no_hp', 32)->nullable();
$table->date('tanggal_masuk')->nullable();
$table->enum('status_kerja', ['tetap','kontrak','magang','outsourcing'])->nullable();


// opsional HR
$table->string('alamat')->nullable();
$table->string('npwp')->nullable();
$table->string('bpjs_ketenagakerjaan')->nullable();
$table->string('bpjs_kesehatan')->nullable();


$table->timestamps();
$table->softDeletes();
});
}


public function down(): void {
Schema::dropIfExists('hr_employee_profiles');
}
};