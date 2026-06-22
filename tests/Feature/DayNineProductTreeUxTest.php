<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DayNineProductTreeUxTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_deep_product_detail_page_shows_breadcrumbs_and_catalog_path(): void
    {
        [$root, $child, $grandchild] = $this->createDeepProductTree();

        $this->assertSame(
            'Digital Mobile Comm / CVMS / Multi Source Video Streaming',
            $grandchild->getCatalogPath()
        );

        $this->actingAs($this->admin)
            ->get(route('admin.products.show', $grandchild))
            ->assertOk()
            ->assertSeeInOrder([
                'Digital Mobile Comm',
                'CVMS',
                'Multi Source Video Streaming',
            ])
            ->assertSee('Digital Mobile Comm / CVMS / Multi Source Video Streaming')
            ->assertSee($child->name)
            ->assertSee('Product tree');

        $this->actingAs($this->admin)
            ->get(route('admin.products.show', $root))
            ->assertOk()
            ->assertSee('Child products')
            ->assertSee('Path: Digital Mobile Comm / CVMS')
            ->assertSee('Path: Digital Mobile Comm / CVMS / Multi Source Video Streaming');
    }

    public function test_product_index_uses_tree_controls_without_repeating_path_context(): void
    {
        [$root] = $this->createDeepProductTree();

        Product::query()->create([
            'parent_id' => $root->id,
            'code' => 'ANALYTICS',
            'name' => 'Analytics',
            'description' => 'Sibling product for tree connector coverage.',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Nested product tree')
            ->assertSee('Search products')
            ->assertSee('All statuses')
            ->assertSee('All levels')
            ->assertSee('Expand all')
            ->assertSee('Collapse all')
            ->assertSee('data-tree-depth="0"', false)
            ->assertSee('data-tree-depth="1"', false)
            ->assertSee('data-tree-depth="2"', false)
            ->assertSee('data-tree-terminal="true"', false)
            ->assertSee('data-tree-terminal="false"', false)
            ->assertDontSee('Depth 0')
            ->assertDontSee('Child of Digital Mobile Comm')
            ->assertDontSee('Path: Digital Mobile Comm / CVMS / Multi Source Video Streaming');
    }

    public function test_edit_parent_selector_excludes_self_and_descendants_but_keeps_safe_ancestors(): void
    {
        [$root, $child, $grandchild] = $this->createDeepProductTree();
        $otherRoot = Product::query()->create([
            'code' => 'OTHER',
            'name' => 'Other Root',
            'is_active' => true,
        ]);
        $inactiveRoot = Product::query()->create([
            'code' => 'INACTIVE-PARENT',
            'name' => 'Inactive Parent',
            'is_active' => false,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $root))
            ->assertOk()
            ->assertSee('Select parent')
            ->assertSee('Name, Product ID, or path')
            ->assertDontSee('All statuses')
            ->assertDontSee('All levels')
            ->assertDontSee('<th class="px-5 py-3">Level</th>', false)
            ->assertDontSee('<th class="px-5 py-3">Status</th>', false)
            ->assertSee('\u0022name\u0022:\u0022Other Root', false)
            ->assertDontSee('\u0022name\u0022:\u0022Inactive Parent', false)
            ->assertDontSee('\u0022name\u0022:\u0022CVMS', false)
            ->assertDontSee('\u0022name\u0022:\u0022Multi Source Video Streaming', false);

        $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $grandchild))
            ->assertOk()
            ->assertSee('\u0022name\u0022:\u0022Digital Mobile Comm', false)
            ->assertSee('\u0022name\u0022:\u0022CVMS', false)
            ->assertDontSee('\u0022path\u0022:\u0022Digital Mobile Comm / CVMS / Multi Source Video Streaming', false);
    }

    public function test_backend_validation_prevents_circular_parent_assignments(): void
    {
        [$root, $child, $grandchild] = $this->createDeepProductTree();
        $inactiveParent = Product::query()->create([
            'code' => 'INACTIVE-PARENT',
            'name' => 'Inactive Parent',
            'is_active' => false,
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $root), [
                'parent_id' => $root->id,
                'name' => $root->name,
                'description' => $root->description,
                'is_active' => '1',
            ])
            ->assertSessionHasErrors([
                'parent_id' => 'A product cannot be assigned to itself or one of its descendants.',
            ]);

        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $root), [
                'parent_id' => $grandchild->id,
                'name' => $root->name,
                'description' => $root->description,
                'is_active' => '1',
            ])
            ->assertSessionHasErrors([
                'parent_id' => 'A product cannot be assigned to itself or one of its descendants.',
            ]);

        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $grandchild), [
                'parent_id' => $inactiveParent->id,
                'name' => $grandchild->name,
                'description' => $grandchild->description,
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('parent_id');

        $this->assertNull($root->fresh()->parent_id);

        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $grandchild), [
                'parent_id' => $root->id,
                'name' => $grandchild->name,
                'description' => $grandchild->description,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.products.show', $grandchild, absolute: false));

        $this->assertSame($root->id, $grandchild->fresh()->parent_id);
    }

    /**
     * @return array{Product, Product, Product}
     */
    private function createDeepProductTree(): array
    {
        $root = Product::query()->create([
            'code' => 'DMC',
            'name' => 'Digital Mobile Comm',
            'description' => 'Root catalog family.',
            'is_active' => true,
        ]);
        $child = Product::query()->create([
            'parent_id' => $root->id,
            'code' => 'CVMS',
            'name' => 'CVMS',
            'description' => 'Video management suite.',
            'is_active' => true,
        ]);
        $grandchild = Product::query()->create([
            'parent_id' => $child->id,
            'code' => 'MSVS',
            'name' => 'Multi Source Video Streaming',
            'description' => 'Deeply nested product.',
            'is_active' => true,
        ]);

        return [$root, $child, $grandchild];
    }
}
