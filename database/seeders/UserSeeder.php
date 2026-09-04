<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The test users to seed, so the follow and workout feed features can be tried out.
     *
     * @var array<string, string>
     */
    public const TEST_USER_NAMES = [
        'Anna Kovács' => 'anna@example.com',
        'Bence Nagy' => 'bence@example.com',
        'Csilla Tóth' => 'csilla@example.com',
        'Dávid Szabó' => 'david@example.com',
        'Eszter Varga' => 'eszter@example.com',
        'Ferenc Horváth' => 'ferenc@example.com',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $users = collect(self::TEST_USER_NAMES)->map(
            fn (string $email, string $name) => User::factory()->create(['name' => $name, 'email' => $email])
        )->values();

        // The admin follows the first three test users, so the follow list and feed have content right away.
        $admin->following()->syncWithoutDetaching($users->take(3)->pluck('id'));

        // Each test user follows the next one, so following/followers relations exist between them too.
        $users->each(function (User $user, int $index) use ($users) {
            $next = $users[($index + 1) % $users->count()];

            $user->following()->syncWithoutDetaching([$next->id]);
        });
    }
}
