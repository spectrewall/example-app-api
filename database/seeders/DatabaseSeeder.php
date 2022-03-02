<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $address = Address::factory(1)->create()->first();

        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'teste@teste.com',
            'type' => 'administrator',
            'cpf' => Str::random(11),
            'address_id' => $address->id,
            'password' => Hash::make('password'),

            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        \App\Models\User::factory(10)->create();
    }
}
