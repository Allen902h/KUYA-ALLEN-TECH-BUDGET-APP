<?php

namespace App\Services;

use App\Models\Category;
use App\Models\IncomeCycle;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CsvImportService
{
    public function import(IncomeCycle $cycle, UploadedFile $file): int
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $imported = 0;

        while (($row = fgetcsv($handle, 2000, ',')) !== false) {
            $data = $this->normalizeRow($header ?: [], $row);
            if (! $data || empty($data['amount'])) {
                continue;
            }

            $category = $data['transaction_type'] === 'expense'
                ? $this->resolveCategory($cycle->user_id, $data)
                : null;

            if ($data['transaction_type'] === 'expense' && ! $category) {
                continue;
            }

            Transaction::create([
                'cycle_id' => $cycle->id,
                'category_id' => $category?->id,
                'transaction_type' => $data['transaction_type'],
                'amount' => abs((float) $data['amount']),
                'timestamp' => $data['timestamp'] ?? now(),
                'note' => $data['note'] ?? null,
            ]);

            $imported++;
        }

        fclose($handle);

        return $imported;
    }

    protected function normalizeRow(array $header, array $row): ?array
    {
        $normalizedHeader = array_map(fn ($value) => Str::snake(trim((string) $value)), $header);
        $data = array_combine($normalizedHeader, $row);

        if (! $data) {
            return null;
        }

        $amount = Arr::first([
            $data['amount'] ?? null,
            $data['debit'] ?? null,
            $data['credit'] ?? null,
            $data['transaction_amount'] ?? null,
            $data['value'] ?? null,
        ], fn ($value) => filled($value));

        if ($amount === null) {
            return null;
        }

        $normalizedAmount = (float) preg_replace('/[^0-9\.\-]/', '', (string) $amount);
        $transactionType = $this->detectTransactionType($data, $normalizedAmount);

        $timestamp = Arr::first([
            $data['timestamp'] ?? null,
            $data['date'] ?? null,
            $data['posted_at'] ?? null,
            $data['transaction_date'] ?? null,
        ], fn ($value) => filled($value));

        return [
            'category_id' => $data['category_id'] ?? null,
            'category' => $data['category'] ?? $this->guessCategoryFromText($data['description'] ?? $data['note'] ?? ''),
            'transaction_type' => $transactionType,
            'amount' => $normalizedAmount,
            'timestamp' => $timestamp ? Carbon::parse($timestamp)->toDateTimeString() : now()->toDateTimeString(),
            'note' => $data['note'] ?? $data['description'] ?? $data['details'] ?? null,
        ];
    }

    protected function detectTransactionType(array $data, float $normalizedAmount): string
    {
        $typeHint = Str::lower((string) ($data['transaction_type'] ?? $data['type'] ?? ''));

        if (filled($data['credit'] ?? null) || Str::contains($typeHint, ['income', 'credit', 'deposit'])) {
            return 'income';
        }

        if (filled($data['debit'] ?? null) || Str::contains($typeHint, ['expense', 'debit', 'withdrawal', 'payment'])) {
            return 'expense';
        }

        return $normalizedAmount < 0 ? 'expense' : 'income';
    }

    protected function resolveCategory(int $userId, array $data): ?Category
    {
        if (! empty($data['category_id'])) {
            return Category::where('user_id', $userId)
                ->where('id', $data['category_id'])
                ->first();
        }

        if (! empty($data['category'])) {
            return Category::firstOrCreate(
                [
                    'user_id' => $userId,
                    'name' => Str::title(trim($data['category'])),
                ],
                [
                    'is_fixed' => false,
                    'budget_limit' => null,
                    'due_day' => null,
                ]
            );
        }

        return null;
    }

    protected function guessCategoryFromText(string $value): ?string
    {
        $value = Str::lower($value);

        return match (true) {
            Str::contains($value, ['uber', 'grab', 'train', 'fuel', 'gas', 'transport']) => 'Transport',
            Str::contains($value, ['rent', 'landlord', 'lease']) => 'Rent',
            Str::contains($value, ['netflix', 'cinema', 'spotify', 'movie', 'game']) => 'Entertainment',
            Str::contains($value, ['electric', 'water', 'internet', 'utility', 'bill']) => 'Bills',
            Str::contains($value, ['market', 'grocery', 'restaurant', 'food', 'coffee']) => 'Food',
            Str::contains($value, ['save', 'savings']) => 'Savings',
            default => null,
        };
    }
}
