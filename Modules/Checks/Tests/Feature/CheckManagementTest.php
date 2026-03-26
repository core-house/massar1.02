<?php

namespace Modules\Checks\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Accounts\Models\AccHead;
use Modules\Branches\Models\Branch;
use Modules\Checks\Models\Check;
use Tests\TestCase;

class CheckManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_checks_index(): void
    {
        $user = \App\Models\User::factory()->create();
        Check::factory()->count(5)->create();

        $response = $this->actingAs($user)->get(route('checks.incoming'));

        $response->assertStatus(200);
        $response->assertViewIs('checks::index');
    }

    public function test_can_create_incoming_check(): void
    {
        $user = \App\Models\User::factory()->create();
        $account = AccHead::factory()->create(['code' => '1103001']);
        $portfolio = AccHead::factory()->create(['code' => '110501']);
        $branch = Branch::factory()->create();

        $response = $this->actingAs($user)->get(route('checks.incoming.create'));

        $response->assertStatus(200);
        $response->assertViewIs('checks::create');
    }

    public function test_can_filter_checks_by_status(): void
    {
        $user = \App\Models\User::factory()->create();
        Check::factory()->create(['status' => Check::STATUS_PENDING]);
        Check::factory()->create(['status' => Check::STATUS_CLEARED]);

        $response = $this->actingAs($user)
            ->get(route('checks.incoming', ['status' => Check::STATUS_PENDING]));

        $response->assertStatus(200);
    }

    public function test_can_export_checks(): void
    {
        $user = \App\Models\User::factory()->create();
        Check::factory()->count(5)->create();

        $response = $this->actingAs($user)->get(route('checks.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_storing_outgoing_check_redirects_to_outgoing_index(): void
    {
        $user = \App\Models\User::factory()->create();
        $account = AccHead::factory()->create(['code' => '2103001', 'acc_type' => 2]);
        $portfolio = AccHead::factory()->create(['code' => '210301']);
        $branch = Branch::factory()->create();

        $checkData = [
            'type' => 'outgoing',
            'check_number' => 'CHK-'.rand(1000, 9999),
            'bank_name' => 'البنك الأهلي',
            'account_number' => '1234567890',
            'account_holder_name' => 'شركة الاختبار',
            'amount' => 5000.00,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'pro_date' => now()->format('Y-m-d'),
            'status' => 'pending',
            'acc1_id' => $account->id,
            'portfolio_id' => $portfolio->id,
            'branch_id' => $branch->id,
        ];

        $response = $this->actingAs($user)->post(route('checks.store'), $checkData);

        $response->assertRedirect(route('checks.outgoing'));
        $response->assertSessionHas('success');
    }

    public function test_storing_incoming_check_redirects_to_incoming_index(): void
    {
        $user = \App\Models\User::factory()->create();
        $account = AccHead::factory()->create(['code' => '1103001', 'acc_type' => 1]);
        $portfolio = AccHead::factory()->create(['code' => '110501']);
        $branch = Branch::factory()->create();

        $checkData = [
            'type' => 'incoming',
            'check_number' => 'CHK-'.rand(1000, 9999),
            'bank_name' => 'البنك الأهلي',
            'account_number' => '1234567890',
            'account_holder_name' => 'عميل الاختبار',
            'amount' => 3000.00,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'pro_date' => now()->format('Y-m-d'),
            'status' => 'pending',
            'acc1_id' => $account->id,
            'portfolio_id' => $portfolio->id,
            'branch_id' => $branch->id,
        ];

        $response = $this->actingAs($user)->post(route('checks.store'), $checkData);

        $response->assertRedirect(route('checks.incoming'));
        $response->assertSessionHas('success');
    }

    public function test_updating_outgoing_check_redirects_to_outgoing_index(): void
    {
        $user = \App\Models\User::factory()->create();
        $check = Check::factory()->outgoing()->create();

        $updateData = [
            'amount' => 7500.00,
            'notes' => 'تحديث الشيك',
        ];

        $response = $this->actingAs($user)->put(route('checks.update', $check), $updateData);

        $response->assertRedirect(route('checks.outgoing'));
        $response->assertSessionHas('success');
    }

    public function test_updating_incoming_check_redirects_to_incoming_index(): void
    {
        $user = \App\Models\User::factory()->create();
        $check = Check::factory()->incoming()->create();

        $updateData = [
            'amount' => 4500.00,
            'notes' => 'تحديث الشيك',
        ];

        $response = $this->actingAs($user)->put(route('checks.update', $check), $updateData);

        $response->assertRedirect(route('checks.incoming'));
        $response->assertSessionHas('success');
    }
}
