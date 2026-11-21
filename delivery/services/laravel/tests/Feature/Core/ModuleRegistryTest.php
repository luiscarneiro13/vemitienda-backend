<?php

namespace Tests\Feature\Core;

use App\Core\Modules\ModuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ModuleRegistryTest extends TestCase
{
    use RefreshDatabase;

    private string $fixturesPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturesPath = storage_path('framework/testing/modules-'.uniqid());
        File::makeDirectory($this->fixturesPath.'/Billing', recursive: true);

        File::put($this->fixturesPath.'/Billing/module.json', json_encode([
            'name' => 'Billing',
            'slug' => 'billing',
            'version' => '1.0',
            'description' => 'Facturación',
            'provider' => '',
            'dependencies' => [],
        ]));
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->fixturesPath);

        parent::tearDown();
    }

    public function test_discover_reads_module_json_manifests(): void
    {
        $registry = new ModuleRegistry($this->fixturesPath);

        $manifests = $registry->discover();

        $this->assertArrayHasKey('billing', $manifests);
        $this->assertSame('Billing', $manifests['billing']['name']);
    }

    public function test_sync_persists_discovered_modules_in_the_catalog(): void
    {
        $registry = new ModuleRegistry($this->fixturesPath);

        $synced = $registry->sync();

        $this->assertCount(1, $synced);
        $this->assertDatabaseHas('modules', ['slug' => 'billing', 'name' => 'Billing']);
    }

    public function test_discover_returns_empty_array_when_path_does_not_exist(): void
    {
        $registry = new ModuleRegistry($this->fixturesPath.'/does-not-exist');

        $this->assertSame([], $registry->discover());
    }
}
