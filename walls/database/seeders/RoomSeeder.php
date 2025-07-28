<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Room::create(['room_name' => 'Спальня']);
        Room::create(['room_name' => 'Гостиная']);
        Room::create(['room_name' => 'Кухня']);
        Room::create(['room_name' => 'Детская']);
        Room::create(['room_name' => 'Прихожая']);
        Room::create(['room_name' => 'Кабинет']);
        Room::create(['room_name' => 'Столовая']);
        Room::create(['room_name' => 'Балкон']);
    }
}
