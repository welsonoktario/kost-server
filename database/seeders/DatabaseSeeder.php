<?php

namespace Database\Seeders;

use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /* $user = User::create(
            [
                'username' => 'amar',
                'name' => 'Amar',
                'phone' => '082253129865',
                'type' => 'Owner',
                'password' => Hash::make('123')
            ]
        );

        $kost = $user->kost()->create(
            [
                'name' => 'Kost Amar',
                'address' => 'Jl. Jalan No. 8'
            ]
        );

        $types = $kost->roomTypes()->createMany(
            [
                [
                    'name' => 'AC',
                    'room_count' => 8,
                    'cost' => 850000
                ],
                [
                    'name' => 'Non-AC',
                    'room_count' => 8,
                    'cost' => 650000
                ]
            ]
        ); */

        $rooms = [];

        for ($i = 1; $i <= 8; $i++) {
            $rooms[$i - 0] = ['tenant_id' => null];
        }

        RoomType::find(1)->rooms()->createMany($rooms);
        RoomType::find(2)->rooms()->createMany($rooms);
    }
}
