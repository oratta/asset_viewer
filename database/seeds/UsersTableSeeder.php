<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    const TEST_USER_ID = 1;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userArray = ([
            'id' => self::TEST_USER_ID,
            'name' => 'oratta',
            'email' => 'oratta@oratta.com',
            'password' => Hash::make('test1234'),
        ]);

        factory(App\User::class)->states('justRegistered')->create($userArray);
    }
}
