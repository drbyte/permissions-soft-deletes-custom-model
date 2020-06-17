<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Tests\TestCase;
use Spatie\Permission\Models\Permission as OriginalPermission;
use Spatie\Permission\Models\Role as OriginalRole;
use Spatie\Permission\PermissionRegistrar;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $permission = OriginalPermission::create(['name' => 'edit articles']);
        $role1 = OriginalRole::create(['name' => 'writer']);
        $role1->givePermissionTo($permission->name);

        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

    /** @test */
    public function it_can_create_roles_with_original_model()
    {
        $role = OriginalRole::findByName('writer');
        $this->assertSame('writer', $role->name);
    }

    /** @test */
    public function it_can_delete_roles_with_original_model()
    {
        $role = OriginalRole::findByName('writer');
        $role->delete();

        $this->expectException(RoleDoesNotExist::class);
        $role = OriginalRole::findByName('writer');

        $this->assertNull($role);
    }
}
