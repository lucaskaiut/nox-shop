<?php

namespace Tests\Feature\Admin\Customer;

use App\Modules\Customer\Domain\Models\Customer;
use App\Modules\Customer\Domain\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\TestAdminCase;

class CustomerCrudTest extends TestAdminCase
{
    use RefreshDatabase;

    public function test_can_create_customer()
    {
        $response = $this->postJson('/api/customer', [
            'first_name' => 'Lucas',
            'last_name' => 'Kaiut',
            'email' => 'lucas@example.com',
            'password' => 'password',
            'type' => 'person',
            'document' => '12345678900',
            'birthdate' => '2000-01-01',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('customers', [
            'email' => 'lucas@example.com',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_list_customers()
    {
        Customer::factory()->count(2)->create(['company_id' => $this->company->id]);

        $response = $this->getJson('/api/customer');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_can_show_customer()
    {
        $customer = Customer::factory()->create(['company_id' => $this->company->id]);

        $response = $this->getJson("/api/customer/{$customer->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['email' => $customer->email]);
    }

    public function test_can_update_customer()
    {
        $customer = Customer::factory()->create(['company_id' => $this->company->id]);

        $response = $this->putJson("/api/customer/{$customer->id}", [
            'first_name' => 'Atualizado',
            'last_name' => 'Silva',
            'email' => 'atualizado@example.com',
            'type' => 'company',
            'document' => '12345678000199',
            'birthdate' => '1995-05-20',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'first_name' => 'Atualizado',
            'email' => 'atualizado@example.com',
        ]);
    }

    public function test_can_delete_customer()
    {
        $customer = Customer::factory()->create(['company_id' => $this->company->id]);

        $response = $this->deleteJson("/api/customer/{$customer->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }

    public function test_can_add_address_to_customer()
    {
        $customer = Customer::factory()->create(['company_id' => $this->company->id]);

        $response = $this->postJson("/api/address", [
            'name' => 'Casa',
            'street' => 'Rua das Flores',
            'number' => '123',
            'complement' => 'Apto 101',
            'district' => 'Centro',
            'postcode' => '12345678',
            'city' => 'São Paulo',
            'state' => 'SP',
            'country' => 'BR',
            'customer_id' => $customer->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('addresses', [
            'customer_id' => $customer->id,
            'name' => 'Casa',
        ]);
    }

    public function test_can_list_customer_addresses()
    {
        $customer = Customer::factory()->create(['company_id' => $this->company->id]);
        Address::factory()->count(2)->create(['customer_id' => $customer->id]);

        $response = $this->getJson("/api/address");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_can_update_customer_address()
    {
        $customer = Customer::factory()->create(['company_id' => $this->company->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id]);

        $response = $this->putJson("/api/address/{$address->id}", [
            'name' => 'Trabalho',
            'street' => 'Av. Central',
            'number' => '456',
            'complement' => 'Sala 10',
            'district' => 'Bairro Novo',
            'postcode' => '87654321',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'country' => 'BR',
            'customer_id' => $customer->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'name' => 'Trabalho',
        ]);
    }

    public function test_can_delete_customer_address()
    {
        $customer = Customer::factory()->create(['company_id' => $this->company->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id]);

        $response = $this->deleteJson("/api/address/{$address->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('addresses', [
            'id' => $address->id,
        ]);
    }
}
