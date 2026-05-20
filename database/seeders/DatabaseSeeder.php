<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Src\Domain\Driver\Models\Entities\Driver;
use Src\Domain\Order\Models\Entities\Order;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'WINCH Dispatcher',
            'email' => 'dispatcher@winch.test',
        ]);

        $token = $user->createToken('demo')->plainTextToken;

        // A pool of online drivers ready to receive work, plus a few offline
        // ones to prove the "available" filter excludes them.
        Driver::factory()->count(8)->create();
        Driver::factory()->offline()->count(2)->create();

        // Pending orders waiting on the dispatcher's "active orders" screen.
        Order::factory()->count(15)->create();

        $this->command->newLine();
        $this->command->info('Demo API token (Bearer): '.$token);
        $this->command->newLine();
    }
}
