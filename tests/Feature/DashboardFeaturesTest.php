<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\IncomeCycle;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DashboardFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_new_budgeting_sections(): void
    {
        $user = User::factory()->create([
            'currency_pref' => 'USD',
            'savings_goal_percentage' => 20,
            'monthly_budget_limit' => 3000,
        ]);

        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Bills',
            'is_fixed' => true,
            'budget_limit' => 900,
            'due_day' => now()->addDays(2)->day,
        ]);

        Category::create([
            'user_id' => $user->id,
            'name' => 'Rent',
            'is_fixed' => true,
            'budget_limit' => 1200,
            'due_day' => now()->addDays(1)->day,
        ]);

        $cycle = IncomeCycle::create([
            'user_id' => $user->id,
            'amount' => 4000,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]);

        Transaction::create([
            'cycle_id' => $cycle->id,
            'category_id' => $category->id,
            'transaction_type' => 'expense',
            'amount' => 350,
            'timestamp' => now(),
            'note' => 'Electric bill',
        ]);

        SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Emergency fund',
            'target_amount' => 5000,
            'current_amount' => 1500,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Add income or expense');
        $response->assertSee('Savings progress');
        $response->assertSee('Transaction history');
        $response->assertSee('Download Backup');
        $response->assertSee('Rent is due in');
    }

    public function test_user_can_store_income_transaction_and_download_backup(): void
    {
        $user = User::factory()->create([
            'currency_pref' => 'USD',
            'savings_goal_percentage' => 15,
            'monthly_budget_limit' => 2500,
        ]);

        $cycle = IncomeCycle::create([
            'user_id' => $user->id,
            'amount' => 3000,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]);

        $this->actingAs($user)->post(route('transactions.store'), [
            'cycle_id' => $cycle->id,
            'transaction_type' => 'income',
            'amount' => 250,
            'timestamp' => now()->toDateTimeString(),
            'note' => 'Freelance work',
        ])->assertRedirect();

        $this->assertDatabaseHas('transactions', [
            'cycle_id' => $cycle->id,
            'transaction_type' => 'income',
            'amount' => 250,
        ]);

        $backupResponse = $this->actingAs($user)->get(route('dashboard.backup'));

        $backupResponse->assertOk();
        $backupResponse->assertHeader('content-type', 'application/json');
    }

    public function test_user_can_update_budget_settings_create_category_and_manage_savings_goal(): void
    {
        $user = User::factory()->create([
            'currency_pref' => 'USD',
            'savings_goal_percentage' => 15,
            'monthly_budget_limit' => 2500,
        ]);

        $this->actingAs($user)->post(route('dashboard.settings.update'), [
            'currency_pref' => 'PHP',
            'savings_goal_percentage' => 25,
            'monthly_budget_limit' => 4200,
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'currency_pref' => 'PHP',
            'savings_goal_percentage' => 25,
            'monthly_budget_limit' => 4200,
        ]);

        $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Utilities',
            'budget_limit' => 900,
            'due_day' => 18,
            'is_fixed' => '1',
        ])->assertRedirect();

        $category = Category::where('user_id', $user->id)->where('name', 'Utilities')->firstOrFail();

        $this->actingAs($user)->post(route('savings-goals.store'), [
            'name' => 'School fees',
            'target_amount' => 10000,
            'current_amount' => 2500,
            'target_date' => now()->addMonths(6)->toDateString(),
            'notes' => 'Semester reserve',
        ])->assertRedirect();

        $goal = SavingsGoal::where('user_id', $user->id)->where('name', 'School fees')->firstOrFail();

        $this->actingAs($user)->put(route('savings-goals.update', $goal), [
            'name' => 'School fees',
            'target_amount' => 10000,
            'current_amount' => 4000,
            'target_date' => now()->addMonths(6)->toDateString(),
            'notes' => 'Updated reserve',
        ])->assertRedirect();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'due_day' => 18,
            'is_fixed' => 1,
        ]);

        $this->assertDatabaseHas('savings_goals', [
            'id' => $goal->id,
            'current_amount' => 4000,
            'notes' => 'Updated reserve',
        ]);
    }

    public function test_user_can_import_csv_with_expense_and_income_rows(): void
    {
        $user = User::factory()->create();

        Category::create([
            'user_id' => $user->id,
            'name' => 'Food',
            'is_fixed' => false,
            'budget_limit' => 1000,
        ]);

        $cycle = IncomeCycle::create([
            'user_id' => $user->id,
            'amount' => 5000,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]);

        $csv = <<<CSV
date,description,amount
2026-04-01,Coffee shop,-180.50
2026-04-02,Client payment,1500.00
CSV;

        $file = UploadedFile::fake()->createWithContent('transactions.csv', $csv);

        $this->actingAs($user)->post(route('csv-import.store'), [
            'cycle_id' => $cycle->id,
            'csv_file' => $file,
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('transactions', [
            'cycle_id' => $cycle->id,
            'transaction_type' => 'expense',
            'amount' => 180.50,
        ]);

        $this->assertDatabaseHas('transactions', [
            'cycle_id' => $cycle->id,
            'transaction_type' => 'income',
            'amount' => 1500.00,
        ]);
    }

    public function test_user_can_update_and_delete_income_cycle(): void
    {
        $user = User::factory()->create();

        $cycle = IncomeCycle::create([
            'user_id' => $user->id,
            'amount' => 5000,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]);

        $this->actingAs($user)->put(route('income-cycles.update', $cycle), [
            'amount' => 6500,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ])->assertRedirect(route('dashboard', ['cycle' => $cycle->id]));

        $this->assertDatabaseHas('income_cycles', [
            'id' => $cycle->id,
            'amount' => 6500,
        ]);

        $this->actingAs($user)->delete(route('income-cycles.destroy', $cycle))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('income_cycles', [
            'id' => $cycle->id,
        ]);
    }
}
