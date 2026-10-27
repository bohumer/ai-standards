<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Product\Create;
use App\Livewire\Admin\Product\Delete;
use App\Livewire\Admin\Product\Edit;
use App\Models\Draft;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_multilingual_product_and_clear_draft(): void
    {
        $admin = User::factory()->create(['role' => 1]);

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name.sk', 'Remeselný syr')
            ->set('name.en', 'Artisan cheese')
            ->set('price', 12.50)
            ->assertSet('hasDraft', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'user_id' => $admin->id,
            'price' => 12.50,
        ]);

        $product = Product::query()->firstOrFail();
        $this->assertSame('Remeselný syr', $product->getTranslation('name', 'sk'));
        $this->assertSame('Artisan cheese', $product->getTranslation('name', 'en'));

        $this->assertSame(0, Draft::query()->where('user_id', $admin->id)->count());
    }

    public function test_product_creation_validates_required_slovak_name(): void
    {
        $admin = User::factory()->create(['role' => 1]);

        Livewire::actingAs($admin)
            ->test(Create::class)
            ->set('name.sk', '')
            ->set('price', -5)
            ->call('save')
            ->assertHasErrors(['name.sk', 'price']);
    }

    public function test_unauthorized_user_cannot_update_or_delete_foreign_product(): void
    {
        $owner = User::factory()->create(['role' => 3]);
        $intruder = User::factory()->create(['role' => 9]);
        $product = Product::factory()->create(['user_id' => $owner->id]);

        Livewire::actingAs($intruder)
            ->test(Edit::class, ['product' => $product])
            ->assertForbidden();

        Livewire::actingAs($intruder)
            ->test(Delete::class)
            ->call('confirmDelete', $product->id)
            ->assertForbidden();

        $this->assertNotSoftDeleted('products', ['id' => $product->id]);
    }
}
