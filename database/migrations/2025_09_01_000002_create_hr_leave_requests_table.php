<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void {
Schema::create('hr_leave_requests', function (Blueprint $t) {
$t->id();
// pemohon & struktur (longgar)
$t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
$t->foreignId('employee_id')->nullable()->constrained('hr_employee_profiles')->nullOnDelete();
$t->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
$t->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();


$t->enum('jenis', ['Cuti Tahunan','Izin','Sakit','Cuti Melahirkan','Cuti Menikah','Lainnya'])->default('Cuti Tahunan');
$t->date('tanggal_mulai');
$t->date('tanggal_selesai');
$t->unsignedInteger('total_hari')->default(1);
$t->text('alasan')->nullable();


// approval chain
$t->enum('status', ['draft','diajukan','disetujui_spv','ditolak_spv','disetujui_manager','ditolak_manager','dibatalkan'])
->default('diajukan')->index();
$t->foreignId('approved_spv_id')->nullable()->constrained('users')->nullOnDelete();
$t->timestamp('approved_spv_at')->nullable();
$t->foreignId('approved_manager_id')->nullable()->constrained('users')->nullOnDelete();
$t->timestamp('approved_manager_at')->nullable();


$t->timestamps();
$t->softDeletes();
});
}


public function down(): void {
Schema::dropIfExists('hr_leave_requests');
}
};