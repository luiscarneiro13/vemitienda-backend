<?php

namespace Tests\Feature\Core;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Core\Media\MediaManager;
use App\Core\Media\Models\Media;
use App\Core\Settings\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class MediaManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_attach_stores_the_file_and_creates_a_media_record(): void
    {
        Storage::fake('public');

        $company = Company::factory()->create();
        $file = UploadedFile::fake()->image('logo.png');

        $media = app(MediaManager::class)->attach($company, $file, 'logo');

        Storage::disk('public')->assertExists($media->path);
        $this->assertSame('logo', $media->collection);
        $this->assertSame($company->getMorphClass(), $media->model_type);
        $this->assertSame($company->id, $media->model_id);
    }

    public function test_delete_removes_the_file_and_the_record(): void
    {
        Storage::fake('public');

        $company = Company::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf');
        $manager = app(MediaManager::class);

        $media = $manager->attach($company, $file, 'documents');
        $path = $media->path;

        $manager->delete($media);

        Storage::disk('public')->assertMissing($path);
        $this->assertModelMissing($media);
    }

    public function test_for_returns_only_media_of_the_given_model_and_collection(): void
    {
        Storage::fake('public');

        $company = Company::factory()->create();
        $manager = app(MediaManager::class);

        $manager->attach($company, UploadedFile::fake()->image('logo.png'), 'logo');
        $manager->attach($company, UploadedFile::fake()->create('contract.pdf'), 'documents');

        $this->assertCount(1, $manager->for($company, 'logo'));
        $this->assertCount(2, $manager->for($company));
    }

    public function test_company_cannot_query_media_belonging_to_another_company(): void
    {
        Storage::fake('public');
        $manager = app(MediaManager::class);

        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $branchA = Branch::factory()->create(['company_id' => $companyA->id]);
        $branchB = Branch::factory()->create(['company_id' => $companyB->id]);

        $mediaCompanyA = $manager->attach($companyA, UploadedFile::fake()->image('logo-a.png'), 'logo');
        $mediaBranchA = $manager->attach($branchA, UploadedFile::fake()->image('branch-a.png'), 'photos');
        $mediaCompanyB = $manager->attach($companyB, UploadedFile::fake()->image('logo-b.png'), 'logo');
        $mediaBranchB = $manager->attach($branchB, UploadedFile::fake()->image('branch-b.png'), 'photos');

        $userA = User::factory()->create(['current_company_id' => $companyA->id]);
        $userA->companies()->attach($companyA->id);

        $this->actingAs($userA);

        $visibleIds = Media::query()->pluck('id');
        $this->assertTrue($visibleIds->contains($mediaCompanyA->id));
        $this->assertTrue($visibleIds->contains($mediaBranchA->id));
        $this->assertFalse($visibleIds->contains($mediaCompanyB->id));
        $this->assertFalse($visibleIds->contains($mediaBranchB->id));

        $this->assertNull(Media::find($mediaCompanyB->id));
        $this->assertCount(0, $manager->for($companyB));
        $this->assertSame($branchA->id, $mediaBranchA->fresh()->model->id);
    }

    public function test_company_cannot_modify_or_delete_foreign_media(): void
    {
        Storage::fake('public');
        $manager = app(MediaManager::class);

        foreach (['View:Media', 'Update:Media', 'Delete:Media'] as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $mediaB = $manager->attach($companyB, UploadedFile::fake()->create('secret.pdf'), 'documents');
        $pathB = $mediaB->path;

        $userA = User::factory()->create(['current_company_id' => $companyA->id]);
        $userA->companies()->attach($companyA->id);
        $userA->givePermissionTo(['View:Media', 'Update:Media', 'Delete:Media']);

        $this->actingAs($userA);

        $this->assertSame(0, Media::whereKey($mediaB->id)->update(['collection' => 'hacked']));
        $this->assertSame(0, Media::whereKey($mediaB->id)->delete());
        $this->assertFalse(Gate::allows('view', $mediaB));
        $this->assertFalse(Gate::allows('update', $mediaB));
        $this->assertFalse(Gate::allows('delete', $mediaB));

        try {
            $manager->delete($mediaB);
            $this->fail('Se esperaba HttpException 403 al borrar Media ajeno.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }

        try {
            $manager->attach($companyB, UploadedFile::fake()->image('intruder.png'), 'logo');
            $this->fail('Se esperaba HttpException 403 al adjuntar Media a empresa ajena.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }

        Storage::disk('public')->assertExists($pathB);
        $this->assertSame('documents', Media::withoutTenancy()->findOrFail($mediaB->id)->collection);
    }

    public function test_global_media_remains_readable_but_protected_from_tenant_mutation(): void
    {
        Storage::fake('public');
        $manager = app(MediaManager::class);

        $setting = Setting::create([
            'key' => 'brand.banner',
            'value' => 'default',
            'type' => 'string',
            'group' => 'general',
        ]);
        $globalMedia = $manager->attach($setting, UploadedFile::fake()->image('banner.png'), 'branding');

        $companyA = Company::factory()->create();
        $userA = User::factory()->create(['current_company_id' => $companyA->id]);
        $userA->companies()->attach($companyA->id);

        $this->actingAs($userA);

        $found = Media::find($globalMedia->id);
        $this->assertNotNull($found);
        $this->assertTrue($found->model->is($setting));
        $this->assertCount(1, $manager->for($setting));

        try {
            $manager->delete($globalMedia);
            $this->fail('Se esperaba HttpException 403 al borrar Media global como usuario de empresa.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }

        Role::findOrCreate('super_admin', 'web');
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin);
        $this->assertTrue($manager->delete($globalMedia));
        $this->assertNull(Media::withoutTenancy()->find($globalMedia->id));
    }
}
