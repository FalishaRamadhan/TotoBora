<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $penda = Facility::create([
            'name'     => 'Penda Medical Center',
            'location' => 'Nairobi',
            'contact'  => '+254207909045',
        ]);

        $emmanuel = Facility::create([
            'name'     => 'Emmanuel Medical Center',
            'location' => 'Nairobi',
            'contact'  => '+254751747374',
        ]);

        // admin user
        User::create([
            'name'        => 'System Admin',
            'first_name'  => 'System',
            'last_name'   => 'Admin',
            'email'       => 'admin@totobora.co.ke',
            'password'    => Hash::make('Admin@1234'),
            'role'        => 'admin',
            'facility_id' => $penda->facility_id,
        ]);

        // healthcare workers
        User::create([
            'name'        => 'J. Njoroge',
            'first_name'  => 'Jane',
            'last_name'   => 'Njoroge',
            'email'       => 'j.njoroge@totobora.co.ke',
            'password'    => Hash::make('Worker@1234'),
            'role'        => 'healthcare_worker',
            'facility_id' => $penda->facility_id,
        ]);

        User::create([
            'name'        => 'D. Omondi',
            'first_name'  => 'David',
            'last_name'   => 'Omondi',
            'email'       => 'd.omondi@totobora.co.ke',
            'password'    => Hash::make('Worker@1234'),
            'role'        => 'healthcare_worker',
            'facility_id' => $emmanuel->facility_id,
        ]);
    }
}
