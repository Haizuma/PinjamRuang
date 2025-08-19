<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminRolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $admin_roles = [
            [
                'id' => 1,
                'name' => 'Administrator',
                'slug' => 'administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Tata Usaha',
                'slug' => 'tata-usaha',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Kepala Bidang',
                'slug' => 'kepala-bidang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Pegawai',
                'slug' => 'pegawai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Cek apakah tabel sudah terisi
        if (\DB::table('admin_roles')->count() === 0) {
            \DB::table('admin_roles')->insert($admin_roles);
        } else {
            echo "\e[31mTabel admin_roles tidak kosong, seeding dibatalkan.\n";
        }
    }
}
