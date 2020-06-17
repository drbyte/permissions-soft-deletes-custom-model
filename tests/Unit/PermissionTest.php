<?php

namespace Tests\Unit;

use \App\Role as CustomRole;
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


    /** @test */
    public function it_can_create_roles_with_custom_model()
    {
        $role = CustomRole::findByName('writer');
        $this->assertSame('writer', $role->name);
    }

    /** @test */
    public function it_can_soft_delete_roles_with_custom_model()
    {
        $role = CustomRole::findByName('writer');
        $role->delete();

        $this->assertTrue($role->trashed());

        $this->expectException(RoleDoesNotExist::class);
        $role = CustomRole::findByName('writer');

        $this->assertNull($role);
    }

    /** @test */
    public function soft_deleted_role_can_be_restored()
    {
        $role = CustomRole::findByName('writer');
        $role->delete();

        $this->assertTrue($role->trashed());

        $role = CustomRole::withTrashed()->where('name', 'writer')->first();
        $this->assertSame('writer', $role->name);

        $role->restore();

        $this->assertFalse($role->trashed());

        $role = CustomRole::findByName('writer');
        $this->assertSame('writer', $role->name);
        $this->assertFalse($role->trashed());
    }
}
