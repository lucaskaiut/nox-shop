<?php

namespace Tests\Feature\Website\Customer;

use App\Modules\Customer\Domain\Models\Customer;
use App\Modules\Customer\Domain\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Website\TestWebsiteCase;

class CustomerCrudTest extends TestWebsiteCase
{
    use RefreshDatabase;

    public function test_can_create_customer()
    {
        $response = $this->postJson('/api/customer/register', [
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

    public function test_cant_list_customers()
    {
        $response = $this->getJson('/api/customer');

        $response->assertStatus(403);
    }

    public function test_can_show_customer()
    {
        $response = $this->getJson("/api/customer/{$this->authUser->customer()->first()->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => $this->authUser->customer()->first()->email]);
    }

    public function test_cant_show_other_customer()
    {
        $customer = Customer::factory()->create(['company_id' => $this->company->id]);

        $response = $this->getJson("/api/customer/{$customer->id}");

        $response->assertStatus(404);
    }

    public function test_cant_update_other_customer()
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

        $response->assertStatus(404);
    }

    public function test_can_update_customer()
    {
        $response = $this->putJson("/api/customer/{$this->authUser->customer()->first()->id}", [
            'first_name' => 'Atualizado',
            'last_name' => 'Silva',
            'email' => 'atualizado@example.com',
            'type' => 'company',
            'document' => '12345678000199',
            'birthdate' => '1995-05-20',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('customers', [
            'id' => $this->authUser->customer()->first()->id,
            'first_name' => 'Atualizado',
            'email' => 'atualizado@example.com',
        ]);
    }

    public function test_cant_delete_customer()
    {
        $customerId = $this->authUser->customer()->first()->id;
        $response = $this->deleteJson("/api/customer/$customerId");

        $response->assertStatus(403);
    }

    public function test_cant_add_address_to_other_customer()
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

        $response->assertStatus(201)->assertJsonFragment(['customer_id' => $this->authUser->customer()->first()->id]);
    }

    public function test_can_add_address_to_customer()
    {
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
            'customer_id' => $this->authUser->customer()->first()->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('addresses', [
            'customer_id' => $this->authUser->customer()->first()->id,
            'name' => 'Casa',
        ]);
    }

    public function test_cant_list_other_customer_addresses()
    {
        $customer = Customer::factory()->create(['company_id' => $this->company->id]);
        Address::factory()->count(2)->create(['customer_id' => $customer->id]);

        $response = $this->getJson("/api/address");

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function test_can_list_customer_addresses()
    {
        Address::factory()->count(2)->create(['customer_id' => $this->authUser->customer()->first()->id]);

        $response = $this->getJson("/api/address");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_can_update_customer_address()
    {
        $address = Address::factory()->create(['customer_id' => $this->authUser->customer()->first()->id]);

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
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'name' => 'Trabalho',
        ]);
    }

    public function test_cant_update_other_customer_address()
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

        $response->assertStatus(404);
    }

    public function test_can_delete_customer_address()
    {
        $address = Address::factory()->create(['customer_id' => $this->authUser->customer()->first()->id]);

        $response = $this->deleteJson("/api/address/{$address->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('addresses', [
            'id' => $address->id,
        ]);
    }

    public function test_cant_delete_other_customer_address()
    {
        $customer = Customer::factory()->create(['company_id' => $this->company->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id]);

        $response = $this->deleteJson("/api/address/{$address->id}");

        $response->assertStatus(404);
    }
}
