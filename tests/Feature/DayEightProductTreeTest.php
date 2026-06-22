<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DayEightProductTreeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_product_model_supports_recursive_helpers_and_scopes(): void
    {
        $root = Product::query()->create([
            'code' => 'ROOT',
            'name' => 'Root Product',
            'is_active' => true,
        ]);
        $inactiveRoot = Product::query()->create([
            'code' => 'INACTIVE',
            'name' => 'Inactive Product',
            'is_active' => false,
        ]);
        $child = Product::query()->create([
            'parent_id' => $root->id,
            'code' => 'ROOT-CHILD',
            'name' => 'Child Product',
            'is_active' => true,
        ]);
        $grandchild = Product::query()->create([
            'parent_id' => $child->id,
            'code' => 'ROOT-CHILD-GRAND',
            'name' => 'Grandchild Product',
            'is_active' => true,
        ]);

        $this->assertTrue($child->parent->is($root));
        $this->assertTrue($root->subProducts()->first()->is($child));
        $this->assertTrue(Product::query()->main()->pluck('id')->contains($root->id));
        $this->assertFalse(Product::query()->main()->pluck('id')->contains($child->id));
        $this->assertTrue(Product::query()->active()->pluck('id')->contains($root->id));
        $this->assertFalse(Product::query()->active()->pluck('id')->contains($inactiveRoot->id));

        $this->assertEqualsCanonicalizing([$child->id, $grandchild->id], $root->getAllDescendantIds());

        $flatDescendants = $root->getFlatDescendants();

        $this->assertSame(['Child Product', 'Grandchild Product'], $flatDescendants->pluck('name')->all());
        $this->assertSame([0, 1], $flatDescendants->pluck('tree_depth')->all());
    }

    public function test_admin_can_create_top_level_and_child_products_with_generated_codes(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('Product ID')
            ->assertSee('Products can share names, but product IDs must be unique.')
            ->assertSee('data-existing-product-ids', false);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'name' => 'Mimsan Platform',
                'description' => 'Main product family.',
                'is_active' => '1',
            ]);

        $root = Product::query()->where('code', 'MIMSAN-PLATFORM')->firstOrFail();

        $response->assertRedirect(route('admin.products.show', $root, absolute: false));
        $this->assertNull($root->parent_id);

        $childResponse = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'parent_id' => $root->id,
                'name' => 'Mimsan Platform',
                'description' => 'Desktop edition.',
                'is_active' => '1',
            ]);

        $child = Product::query()->where('code', 'MIMSAN-PLATFORM-2')->firstOrFail();

        $childResponse->assertRedirect(route('admin.products.show', $child, absolute: false));
        $this->assertTrue($child->parent->is($root));

        $this->actingAs($this->admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Nested product tree')
            ->assertSee('Mimsan Platform')
            ->assertSee('MIMSAN-PLATFORM')
            ->assertSee('MIMSAN-PLATFORM-2')
            ->assertSee('Search products')
            ->assertSee('Expand all')
            ->assertSee('data-tree-depth="0"', false)
            ->assertSee('data-tree-depth="1"', false)
            ->assertSee('View')
            ->assertSee('Edit')
            ->assertSee('Delete');
    }

    public function test_product_id_can_be_entered_manually_and_must_be_unique(): void
    {
        $existing = Product::query()->create([
            'code' => 'MANUAL-ID',
            'name' => 'Shared Product Name',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'code' => ' custom product 01 ',
                'name' => 'Shared Product Name',
                'description' => 'Duplicate names are allowed when product IDs differ.',
                'is_active' => '1',
            ]);

        $product = Product::query()->where('code', 'CUSTOM-PRODUCT-01')->firstOrFail();

        $response->assertRedirect(route('admin.products.show', $product, absolute: false));
        $this->assertSame($existing->name, $product->name);

        $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'code' => ' manual id ',
                'name' => 'Different Product Name',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('code');

        $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'code' => '!!!',
                'name' => 'Invalid Manual Product ID',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_admin_can_view_edit_and_deactivate_product(): void
    {
        $root = Product::query()->create([
            'code' => 'PLATFORM',
            'name' => 'Platform',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.products.show', $root))
            ->assertOk()
            ->assertSee('PLATFORM')
            ->assertSee('Top-level product')
            ->assertSee('Child products');

        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $root), [
                'name' => 'Platform Updated',
                'description' => 'Updated description.',
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.products.show', $root, absolute: false));

        $this->assertDatabaseHas('products', [
            'id' => $root->id,
            'code' => 'PLATFORM',
            'name' => 'Platform Updated',
            'description' => 'Updated description.',
            'is_active' => false,
        ]);
    }

    public function test_admin_cannot_assign_product_under_itself_or_descendant(): void
    {
        $root = Product::query()->create([
            'code' => 'ROOT',
            'name' => 'Root',
        ]);
        $child = Product::query()->create([
            'parent_id' => $root->id,
            'code' => 'CHILD',
            'name' => 'Child',
        ]);
        $grandchild = Product::query()->create([
            'parent_id' => $child->id,
            'code' => 'GRANDCHILD',
            'name' => 'Grandchild',
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $root), [
                'parent_id' => $root->id,
                'name' => 'Root',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('parent_id');

        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $root), [
                'parent_id' => $grandchild->id,
                'name' => 'Root',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('parent_id');
    }

    public function test_product_delete_blocks_children_and_allows_leaf_products(): void
    {
        $root = Product::query()->create([
            'code' => 'ROOT',
            'name' => 'Root',
        ]);
        $child = Product::query()->create([
            'parent_id' => $root->id,
            'code' => 'CHILD',
            'name' => 'Child',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $root))
            ->assertRedirect(route('admin.products.show', $root, absolute: false))
            ->assertSessionHasErrors('product');

        $this->assertNotNull($root->fresh());

        $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $child))
            ->assertRedirect(route('admin.products.index', absolute: false))
            ->assertSessionHas('status', 'Product deleted.');

        $this->assertNull($child->fresh());
    }
}
