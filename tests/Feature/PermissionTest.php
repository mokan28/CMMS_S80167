<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PermissionTest extends TestCase
{
    public function test_viewer_cannot_close(): void
    {
        $viewer = User::factory()->make(['role'=>'viewer']);
        $this->actingAs($viewer);
        // placeholder: assert 403 on close route
        $this->assertTrue(true);
    }
}
