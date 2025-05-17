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
                    'value' => config('mail.mailers.smtp.host'),
                ],
                [
                    'type' => 'mail',
                    'key' => 'port',
                    'value' => config('mail.mailers.smtp.port'),
                ],
                [
                    'type' => 'mail',
                    'key' => 'username',
                    'value' => config('mail.mailers.smtp.username'),
                ],
                [
                    'type' => 'mail',
                    'key' => 'password',
                    'value' => config('mail.mailers.smtp.password'),
                ],
                [
                    'type' => 'mail',
                    'key' => 'from_name',
                    'value' => config('mail.from.name'),
                ],
                [
                    'type' => 'mail',
                    'key' => 'from_address',
                    'value' => config('mail.from.address'),
                ],
            ]);
        });
    }
}
