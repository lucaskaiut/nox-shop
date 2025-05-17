<?php

namespace Database\Seeders;

use App\Modules\Company\Domain\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $company = Company::create([
                'name' => 'Loja Teste',
                'domain' => 'lojateste.com',
                'document' => '99999999999999',
                'contact_email' => 'contato@lojateste.com',
            ]);

            app('company')->registerCompany($company);

            $company->users()->create([
                'name' => 'Fulano de Tal',
                'email' => 'fulano@lojateste.com',
                'password' => Hash::make('abc@123'),
            ]);

            $company->settings()->createMany([
                [
                    'type' => 'mail',
                    'key' => 'host',
                    'value' => 'smtp.gmail.com',
                ],
                [
                    'type' => 'mail',
                    'key' => 'port',
                    'value' => 465,
                ],
                [
                    'type' => 'mail',
                    'key' => 'username',
                    'value' => 'lucas.kaiut@gmail.com',
                ],
                [
                    'type' => 'mail',
                    'key' => 'password',
                    'value' => 'dlrrhybfwwulxscu',
                ],
                [
                    'type' => 'mail',
                    'key' => 'from_address',
                    'value' => 'contato@lojateste.com',
                ],
                [
                    'type' => 'mail',
                    'key' => 'from_name',
                    'value' => 'Loja Teste',
                ],
            ]);
        });
    }
}
