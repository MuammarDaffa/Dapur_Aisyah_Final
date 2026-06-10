<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\CateringService;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->service = CateringService::create([
            'name' => 'Test Service',
            'slug' => 'test-service',
            'description' => 'Service Description',
            'min_portion' => 10,
            'base_price' => 50000,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_active_product(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/products', [
            'catering_service_id' => $this->service->id,
            'name' => 'Active Product',
            'description' => 'Product Description',
            'price' => 20000,
            'is_active' => 1,
        ]);

        $response->assertRedirect('/admin/products');
        
        $this->assertDatabaseHas('products', [
            'name' => 'Active Product',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_inactive_product(): void
    {
        // When checkbox is unchecked, it is not present in the post data
        $response = $this->actingAs($this->admin)->post('/admin/products', [
            'catering_service_id' => $this->service->id,
            'name' => 'Inactive Product',
            'description' => 'Product Description',
            'price' => 20000,
            // 'is_active' is omitted
        ]);

        $response->assertRedirect('/admin/products');
        
        $this->assertDatabaseHas('products', [
            'name' => 'Inactive Product',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_update_product_to_inactive(): void
    {
        $product = Product::create([
            'catering_service_id' => $this->service->id,
            'name' => 'Product to Edit',
            'slug' => 'product-to-edit',
            'description' => 'Description',
            'price' => 20000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/products/{$product->id}", [
            'catering_service_id' => $this->service->id,
            'name' => 'Product to Edit',
            'description' => 'Description',
            'price' => 25000,
            // 'is_active' is omitted (unchecked)
        ]);

        $response->assertRedirect('/admin/products');
        
        $product->refresh();
        $this->assertFalse($product->is_active);
    }

    public function test_inactive_products_are_not_visible_to_customers(): void
    {
        $activeProduct = Product::create([
            'catering_service_id' => $this->service->id,
            'name' => 'Active Product',
            'slug' => 'active-product',
            'price' => 20000,
            'is_active' => true,
        ]);

        $inactiveProduct = Product::create([
            'catering_service_id' => $this->service->id,
            'name' => 'Inactive Product',
            'slug' => 'inactive-product',
            'price' => 20000,
            'is_active' => false,
        ]);

        $activeProducts = Product::active()->get();
        
        $this->assertTrue($activeProducts->contains($activeProduct));
        $this->assertFalse($activeProducts->contains($inactiveProduct));
    }
}
