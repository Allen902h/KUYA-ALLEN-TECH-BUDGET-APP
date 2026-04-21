<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportCsvRequest;
use App\Models\IncomeCycle;
use App\Services\CsvImportService;

class CsvImportController extends Controller
{
    public function __construct(private CsvImportService $csvImportService)
    {
    }

    public function index()
    {
        $cycles = auth()->user()->incomeCycles()->orderByDesc('start_date')->get();

        return view('csv-import', compact('cycles'));
    }

    public function store(ImportCsvRequest $request)
    {
        $cycle = IncomeCycle::where('id', $request->cycle_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $count = $this->csvImportService->import($cycle, $request->file('csv_file'));

        return redirect()->route('dashboard')->with('success', "CSV imported successfully. {$count} transactions added.");
    }
}