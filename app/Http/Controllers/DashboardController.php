<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBudgetSettingsRequest;
use App\Models\IncomeCycle;
use App\Services\BudgetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private BudgetService $budgetService)
    {
    }

    public function index(Request $request)
    {
        $user = Auth::user()->loadMissing('categories');

        $cycle = IncomeCycle::with(['user.categories', 'transactions.category'])
            ->where('user_id', $user->id)
            ->when($request->cycle, fn ($query, $cycleId) => $query->where('id', $cycleId))
            ->when(! $request->cycle, function ($query) {
                $query->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now());
            })
            ->orderByDesc('start_date')
            ->first();

        if (! $cycle) {
            $cycle = IncomeCycle::with(['user.categories', 'transactions.category'])
                ->where('user_id', $user->id)
                ->orderByDesc('start_date')
                ->first();
        }

        $summary = null;
        $budgetAlerts = collect();
        $dueBillAlerts = collect();
        $filteredTransactions = collect();

        if ($cycle) {
            $summary = $this->budgetService->summary($cycle);
            $budgetAlerts = collect($summary['budgetAlerts']);
            $dueBillAlerts = collect($summary['dueBillAlerts']);
            $filteredTransactions = $this->filterTransactions($cycle, $request);
        }

        $cycles = $user->incomeCycles()->withCount('transactions')->orderByDesc('start_date')->get();
        $categories = $user->categories()->orderBy('name')->get();
        $savingsGoals = $user->savingsGoals()->orderBy('target_date')->orderBy('name')->get();

        return view('dashboard', compact(
            'cycle',
            'summary',
            'cycles',
            'categories',
            'budgetAlerts',
            'dueBillAlerts',
            'filteredTransactions',
            'savingsGoals'
        ));
    }

    public function updateSettings(UpdateBudgetSettingsRequest $request)
    {
        $request->user()->update([
            'currency_pref' => strtoupper($request->currency_pref),
            'savings_goal_percentage' => $request->savings_goal_percentage,
            'monthly_budget_limit' => $request->monthly_budget_limit,
        ]);

        return back()->with('success', 'Budget settings updated successfully.');
    }

    public function exportBackup(Request $request)
    {
        $user = $request->user()->load([
            'categories',
            'incomeCycles.transactions.category',
            'savingsGoals',
        ]);

        $payload = [
            'generated_at' => now()->toIso8601String(),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'currency_pref' => $user->currency_pref,
                'savings_goal_percentage' => $user->savings_goal_percentage,
                'monthly_budget_limit' => $user->monthly_budget_limit,
            ],
            'categories' => $user->categories->map(fn ($category) => [
                'name' => $category->name,
                'is_fixed' => $category->is_fixed,
                'budget_limit' => $category->budget_limit,
                'due_day' => $category->due_day,
            ])->values(),
            'income_cycles' => $user->incomeCycles->map(fn ($cycle) => [
                'amount' => $cycle->amount,
                'start_date' => optional($cycle->start_date)->toDateString(),
                'end_date' => optional($cycle->end_date)->toDateString(),
                'transactions' => $cycle->transactions->map(fn ($transaction) => [
                    'transaction_type' => $transaction->transaction_type,
                    'category' => $transaction->category?->name,
                    'amount' => $transaction->amount,
                    'timestamp' => optional($transaction->timestamp)->toIso8601String(),
                    'note' => $transaction->note,
                ])->values(),
            ])->values(),
            'savings_goals' => $user->savingsGoals->map(fn ($goal) => [
                'name' => $goal->name,
                'target_amount' => $goal->target_amount,
                'current_amount' => $goal->current_amount,
                'target_date' => optional($goal->target_date)->toDateString(),
                'notes' => $goal->notes,
                'is_completed' => $goal->is_completed,
            ])->values(),
        ];

        return response()->streamDownload(function () use ($payload) {
            echo json_encode($payload, JSON_PRETTY_PRINT);
        }, 'budget-backup-'.now()->format('Y-m-d-His').'.json', [
            'Content-Type' => 'application/json',
        ]);
    }

    protected function filterTransactions(IncomeCycle $cycle, Request $request)
    {
        return $cycle->transactions
            ->when($request->filled('transaction_type'), fn ($collection) => $collection->where('transaction_type', $request->transaction_type))
            ->when($request->filled('category_id'), fn ($collection) => $collection->where('category_id', (int) $request->category_id))
            ->when($request->filled('date_from'), fn ($collection) => $collection->filter(fn ($transaction) => $transaction->timestamp && $transaction->timestamp->toDateString() >= $request->date_from))
            ->when($request->filled('date_to'), fn ($collection) => $collection->filter(fn ($transaction) => $transaction->timestamp && $transaction->timestamp->toDateString() <= $request->date_to))
            ->when($request->filled('amount_min'), fn ($collection) => $collection->filter(fn ($transaction) => (float) $transaction->amount >= (float) $request->amount_min))
            ->when($request->filled('amount_max'), fn ($collection) => $collection->filter(fn ($transaction) => (float) $transaction->amount <= (float) $request->amount_max))
            ->when($request->filled('search'), function ($collection) use ($request) {
                $needle = mb_strtolower($request->search);

                return $collection->filter(function ($transaction) use ($needle) {
                    $category = mb_strtolower($transaction->category?->name ?? '');
                    $note = mb_strtolower((string) $transaction->note);

                    return str_contains($category, $needle) || str_contains($note, $needle);
                });
            })
            ->sortByDesc('timestamp')
            ->values();
    }
}
