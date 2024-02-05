<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeesCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('employees_categories')->insert([
            [
                'id' => 1,
                'name' => 'Karyawan Tetap',
                'description' => 'Karyawan yang dipekerjakan secara penuh waktu. Biasanya memiliki kontrak kerja jangka panjang. Mendapatkan tunjangan seperti asuransi, cuti tahunan, dan manfaat lainnya.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Karyawan Kontrak',
                'description' => 'Karyawan dengan kontrak kerja berjangka waktu tertentu. Biasanya dipekerjakan untuk proyek-proyek khusus atau pekerjaan sementara. Manfaat dan tunjangan dapat bervariasi tergantung pada kontrak.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Karyawan Paruh Waktu',
                'description' => 'Karyawan yang bekerja kurang dari waktu kerja penuh (biasanya 40 jam per minggu). Gaji dan manfaat biasanya prorata berdasarkan jumlah jam kerja.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Karyawan Magang',
                'description' => 'Individu yang sedang dalam masa pelatihan atau belajar di perusahaan. Biasanya memiliki kontrak kerja sementara untuk memperoleh pengalaman kerja.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Karyawan Kontrak Paruh Waktu',
                'description' => 'Karyawan dengan kontrak berjangka waktu tertentu yang bekerja paruh waktu. Biasanya digunakan untuk menggantikan karyawan yang sedang cuti atau dalam situasi darurat.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Karyawan Penuh Waktu Sementara',
                'description' => 'Karyawan dengan kontrak berjangka waktu tertentu yang bekerja penuh waktu. Digunakan untuk mengisi posisi sementara dalam proyek khusus atau saat kebutuhan meningkat.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'Karyawan Berdasarkan Proyek',
                'description' => 'Karyawan yang dipekerjakan untuk proyek tertentu. Biasanya digaji berdasarkan proyek yang diselesaikan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'Karyawan Freelance',
                'description' => 'Karyawan independen yang bekerja untuk perusahaan sebagai kontraktor eksternal. Biasanya dibayar berdasarkan proyek atau jam kerja.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'name' => 'Karyawan Konsultan',
                'description' => 'Ahli atau profesional yang disewa oleh perusahaan untuk memberikan konsultasi atau layanan khusus. Biasanya memiliki keahlian dalam bidang tertentu.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'name' => 'Karyawan Tetap dengan Perjanjian Khusus',
                'description' => 'Karyawan tetap yang memiliki perjanjian khusus atau posisi khusus dalam perusahaan. Contohnya, manajer senior atau direktur.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'name' => 'Karyawan Dengan Gaji Harian',
                'description' => 'Karyawan yang dibayar berdasarkan jumlah hari kerja. Biasanya digunakan dalam pekerjaan yang bersifat harian seperti konstruksi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'name' => 'Karyawan Penuh Waktu dengan Jam Kerja Fleksibel',
                'description' => 'Karyawan penuh waktu yang memiliki fleksibilitas dalam jadwal kerja mereka. Biasanya bekerja berdasarkan target atau tugas yang diselesaikan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
