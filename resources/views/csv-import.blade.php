@extends('layouts.app')

@section('title', 'CSV Import | '.config('app.name', 'Budget App'))

@section('content')
<section class="hero-panel compact-hero">
    <div>
        <span class="eyebrow">Bank Statement Import</span>
        <h1>Drop in a CSV and convert it into categorized transactions.</h1>
        <p class="hero-text">
            Import exported bank data with flexible headers like <code>amount</code>, <code>date</code>, <code>timestamp</code>,
            <code>category</code>, or <code>description</code>. The importer can guess categories from merchant text when needed.
        </p>
    </div>
</section>

<section class="workspace-grid">
    <article class="workspace-card">
        <div class="card-header">
            <div>
                <p class="mini-label">Upload</p>
                <h2>Import to a cycle</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('csv-import.store') }}" enctype="multipart/form-data" class="stack-form csv-import-form">
            @csrf

            <label class="field">
                <span>Target cycle</span>
                <select name="cycle_id" required>
                    <option value="">Select income cycle</option>
                    @foreach($cycles as $cycle)
                        <option value="{{ $cycle->id }}">
                            {{ $cycle->start_date->format('M d, Y') }} - {{ $cycle->end_date->format('M d, Y') }} | {{ number_format($cycle->amount, 2) }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="dropzone" for="csv_file">
                <input id="csv_file" type="file" name="csv_file" accept=".csv,.txt" required>
                <strong>Drag and drop your bank CSV here</strong>
                <span>or click to browse your files</span>
            </label>

            <div class="csv-preview" hidden>
                <h3>Preview</h3>
                <pre class="csv-preview-output"></pre>
            </div>

            <button type="submit" class="primary-button full-width">Import Transactions</button>
        </form>
    </article>

    <article class="workspace-card">
        <div class="card-header">
            <div>
                <p class="mini-label">Supported columns</p>
                <h2>Accepted formats</h2>
            </div>
        </div>

        <div class="support-list">
            <div class="support-item">
                <strong>Preferred</strong>
                <code>category,amount,timestamp,note</code>
            </div>
            <div class="support-item">
                <strong>Also works</strong>
                <code>description,amount,date</code>
            </div>
            <div class="support-item">
                <strong>Category override</strong>
                <code>category_id,amount,timestamp,note</code>
            </div>
        </div>

        <p class="soft-note">
            Sensitive banking credentials should never be stored here. Keep exports limited to transactions, descriptions, dates, and amounts only.
        </p>
    </article>
</section>
@endsection
