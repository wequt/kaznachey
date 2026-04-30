<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@mail.ru'],
            [
                'name' => 'Администратор',
                'password' => Hash::make('12345678'),
            ]
        );

        Account::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Основная карта'],
            ['balance' => 10000.00, 'currency' => 'RUB']
        );

        Account::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Наличные'],
            ['balance' => 500.00, 'currency' => 'RUB']
        );

        $categories = [
            ['name' => 'Зарплата', 'type' => 'income'],
            ['name' => 'Продукты', 'type' => 'expense'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['user_id' => $user->id, 'name' => $cat['name']],
                ['type' => $cat['type'], 'icon' => 'default']
            );
        }
    }
}
