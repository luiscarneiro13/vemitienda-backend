<?php

namespace Tests\Feature\Core;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\Contracts\TenantResolverInterface;
use App\Core\Tenancy\Resolvers\AuthenticatedUserTenantResolver;
use App\Core\Tenancy\Resolvers\TenantResolverManager;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_resolver_returns_current_company(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['current_company_id' => $company->id]);
        $this->actingAs($user);

        $resolver = new AuthenticatedUserTenantResolver;

        $this->assertTrue($resolver->resolve()->is($company));
        $this->assertSame('user', $resolver->key());
    }

    public function test_manager_tries_resolvers_in_order_until_one_resolves(): void
    {
        $company = Company::factory()->create();

        $failing = new class implements TenantResolverInterface
        {
            public function key(): string
            {
                return 'failing';
            }

            public function resolve(): ?Company
            {
                return null;
            }
        };

        $succeeding = new class($company) implements TenantResolverInterface
        {
            public function __construct(private readonly Company $company) {}

            public function key(): string
            {
                return 'succeeding';
            }

            public function resolve(): ?Company
            {
                return $this->company;
            }
        };

        $manager = (new TenantResolverManager)
            ->register($failing)
            ->register($succeeding);

        $this->assertTrue($manager->resolve()->is($company));
    }

    public function test_manager_returns_null_when_no_resolver_matches(): void
    {
        $manager = new TenantResolverManager;

        $this->assertNull($manager->resolve());
    }
}
