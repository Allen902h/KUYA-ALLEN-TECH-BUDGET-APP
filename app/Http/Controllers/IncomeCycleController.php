<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncomeCycleRequest;
use App\Http\Requests\UpdateIncomeCycleRequest;
use App\Models\IncomeCycle;

class IncomeCycleController extends Controller
{
    public function store(StoreIncomeCycleRequest $request)
    {
        IncomeCycle::create([
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('dashboard')->with('success', 'Income cycle created successfully.');
    }

    public function update(UpdateIncomeCycleRequest $request, IncomeCycle $incomeCycle)
    {
        abort_unless($incomeCycle->user_id === auth()->id(), 403);

        $incomeCycle->update([
            'amount' => $request->amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('dashboard', ['cycle' => $incomeCycle->id])->with('success', 'Income cycle updated successfully.');
    }

    public function destroy(IncomeCycle $incomeCycle)
    {
        abort_unless($incomeCycle->user_id === auth()->id(), 403);

        $incomeCycle->delete();

        return redirect()->route('dashboard')->with('success', 'Income cycle deleted successfully.');
    }
}
