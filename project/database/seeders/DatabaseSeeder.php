<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         for ($i=0;$i<20;$i++) {
             $faker = Factory::create();
             User::query()->insert([
                 'first_name' => $faker->firstName,
                 'last_name' => $faker->lastName,
                 'email' => $faker->email,
                 'email_verified_at' => Carbon::now(),
                 'token' => Hash::make($faker->text(5)),
                 'password' => Hash::make('password'),
                 'created_at' => Carbon::now(),
                 'updated_at' => Carbon::now()
             ]);
         }

        for ($i=0;$i<10;$i++) {
            $faker = Factory::create();
            Company::query()->insert([
                'title' => $faker->title,
                'phone' => $faker->phoneNumber,
                'description' => $faker->text(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        foreach (User::all() as $user) {
            for ($i=0; $i<2; $i++) {
                DB::table('user_company')->insert([
                    'user_id' => $user->id,
                    'company_id' => DB::table('companies')->inRandomOrder()->first()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
    }
}
