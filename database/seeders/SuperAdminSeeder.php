<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@smpit.sch.id'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('Admin@1234'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Superadmin seeded: admin@smpit.sch.id / Admin@1234');
    }
}
