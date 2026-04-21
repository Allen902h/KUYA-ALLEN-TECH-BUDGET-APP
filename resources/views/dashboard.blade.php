@extends('layouts.app')

@section('title', 'Dashboard | '.config('app.name', 'Budget App'))

@section('body_class', 'app-shell dashboard-shell-body')

@section('content')
@php
    $currency = auth()->user()->currency_pref ?: 'USD';
    $currencySymbols = [
        'PHP' => '₱',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'JPY' => '¥',
    ];
    $currencySymbol = $currencySymbols[strtoupper($currency)] ?? ($currency.' ');
    $user = auth()->user();
    $savingsProgressValue = isset($summary) && $summary
        ? max(min((float) ($summary['savingsProgress'] ?? 0), 100), 0)
        : 0;
    $savingsRingValues = [$savingsProgressValue, max(100 - $savingsProgressValue, 0)];
    $brandName = 'KUYA ALLEN TECH SOLUTIONS';
    $brandTagline = 'Smart budget and tech workflow';
    $brandLogoFile = file_exists(public_path('images/kuya-allen-logo.png'))
        ? 'images/kuya-allen-logo.png'
        : (file_exists(public_path('images/wowlogo.png')) ? 'images/wowlogo.png' : 'icons/icon.svg');
    $brandLogo = asset($brandLogoFile).'?v='.(file_exists(public_path($brandLogoFile)) ? filemtime(public_path($brandLogoFile)) : time());
@endphp

<style>
    .dashboard-shell-body .shell {
        width: calc(100% - 24px);
        max-width: none;
    }

    .dashboard-shell-body .topbar {
        display: none;
    }

    .dashboard-shell-body .page-content {
        padding-top: 8px;
        padding-bottom: 14px;
    }

    .dashboard-shell {
        display: grid;
        grid-template-columns: 240px minmax(0, 1fr);
        grid-template-rows: auto 1fr;
        gap: 0;
        overflow: hidden;
        min-height: calc(100vh - 118px);
        border-radius: 34px;
        border: 1px solid rgba(255, 255, 255, 0.34);
        background:
            linear-gradient(145deg, rgba(255, 250, 246, 0.98), rgba(249, 244, 255, 0.98));
        box-shadow: 0 22px 56px rgba(44, 36, 72, 0.12);
    }

    .dashboard-frame-header {
        grid-column: 1 / -1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 16px 18px;
        background:
            linear-gradient(135deg, rgba(255, 152, 0, 0.9), rgba(255, 140, 109, 0.86) 46%, rgba(120, 101, 255, 0.82));
        border-bottom: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.12);
    }

    .dashboard-brand {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
        color: #fffaf5;
    }

    .dashboard-brand-logo {
        width: 92px;
        height: 92px;
        border-radius: 24px;
        object-fit: contain;
        background: rgba(255, 255, 255, 0.96);
        padding: 8px;
        box-shadow: 0 16px 30px rgba(72, 40, 108, 0.18);
        flex-shrink: 0;
    }

    .dashboard-brand-copy strong {
        display: block;
        font-size: 1.7rem;
        line-height: 1;
        color: #fffaf5;
        letter-spacing: -0.04em;
    }

    .dashboard-brand-copy span {
        display: block;
        margin-top: 4px;
        color: rgba(255, 250, 245, 0.84);
        font-size: 1rem;
    }

    .dashboard-frame-nav {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .dashboard-frame-nav a,
    .dashboard-frame-nav button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: rgba(255, 255, 255, 0.08);
        color: #fffaf5;
        font-weight: 700;
        cursor: pointer;
        transition: background 180ms ease, transform 180ms ease, border-color 180ms ease;
    }

    .dashboard-frame-nav a:hover,
    .dashboard-frame-nav button:hover {
        background: rgba(255, 255, 255, 0.16);
        border-color: rgba(255, 255, 255, 0.24);
        transform: translateY(-1px);
    }

    .dashboard-sidebar {
        padding: 28px 22px;
        background:
            linear-gradient(180deg, #ff9a18 0%, #f58d4a 24%, #a46be3 72%, #795fe0 100%);
        color: #f8fafc;
        display: grid;
        align-content: start;
        gap: 26px;
        border-right: 1px solid rgba(255, 255, 255, 0.08);
    }

    .dashboard-avatar {
        width: 88px;
        height: 88px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        background: linear-gradient(180deg, #fffdf8, #ffe8c9);
        color: #7b341e;
        font-size: 2rem;
        font-weight: 900;
        box-shadow: 0 14px 30px rgba(8, 15, 27, 0.28);
    }

    .dashboard-identity strong,
    .dashboard-sidebar .sidebar-section-title {
        display: block;
    }

    .dashboard-identity strong {
        font-size: 1.4rem;
        letter-spacing: -0.03em;
    }

    .dashboard-identity span {
        display: block;
        margin-top: 6px;
        color: rgba(248, 250, 252, 0.72);
        font-size: 0.92rem;
        word-break: break-word;
    }

    .sidebar-section-title {
        margin-bottom: 10px;
        color: rgba(248, 250, 252, 0.5);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .sidebar-nav,
    .sidebar-mini-stats {
        display: grid;
        gap: 10px;
    }

    .sidebar-link,
    .sidebar-mini-card {
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.08);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 48px;
        padding: 0 14px;
        color: #f8fafc;
        font-weight: 600;
        transition: background 180ms ease, transform 180ms ease;
    }

    .sidebar-link:hover {
        background: rgba(255, 255, 255, 0.12);
        transform: translateX(2px);
    }

    .sidebar-link.active {
        background: linear-gradient(135deg, rgba(255, 248, 239, 0.32), rgba(255, 255, 255, 0.12));
        border-color: rgba(255, 239, 214, 0.4);
        box-shadow: 0 10px 24px rgba(49, 27, 85, 0.12);
    }

    .dashboard-section-anchor {
        scroll-margin-top: 28px;
    }

    .sidebar-link code {
        min-height: 28px;
        padding: 0 10px;
        background: rgba(255, 255, 255, 0.12);
        border: none;
        color: #f8fafc;
        font-size: 0.78rem;
    }

    .sidebar-mini-card {
        padding: 14px;
    }

    .sidebar-mini-card span {
        display: block;
        color: rgba(248, 250, 252, 0.66);
        font-size: 0.82rem;
    }

    .sidebar-mini-card strong {
        display: block;
        margin-top: 6px;
        font-size: 1.3rem;
    }

    .dashboard-main {
        padding: 28px 24px 24px;
        display: grid;
        gap: 18px;
        min-width: 0;
        align-content: start;
        background:
            linear-gradient(180deg, rgba(255, 253, 250, 0.96), rgba(246, 241, 255, 0.96));
    }

    .dashboard-panel-section {
        display: none;
        gap: 18px;
        align-content: start;
    }

    .dashboard-panel-section.is-active {
        display: grid;
    }

    .dashboard-main-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        position: sticky;
        top: 0;
        z-index: 18;
        padding: 10px 4px 18px;
        margin: -6px -4px 0;
        padding-left: 8px;
        padding-right: 8px;
        border-bottom: 1px solid rgba(120, 101, 255, 0.08);
        background: linear-gradient(180deg, rgba(255, 253, 250, 0.98), rgba(248, 244, 255, 0.96));
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 24px rgba(72, 40, 108, 0.06);
    }

    .dashboard-main-header h1 {
        margin: 0;
        font-size: clamp(1.6rem, 2.4vw, 2.2rem);
        line-height: 1.1;
        color: #1e293b;
    }

    .dashboard-main-header p {
        margin: 6px 0 0;
        color: rgba(30, 41, 59, 0.68);
    }

    .header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .insights-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        padding: 0 18px;
        border-radius: 14px;
        border: 1px solid rgba(120, 101, 255, 0.14);
        background: linear-gradient(135deg, rgba(255, 152, 0, 0.1), rgba(120, 101, 255, 0.12));
        color: #2c2151;
        font-weight: 800;
        transition: transform 180ms ease, box-shadow 180ms ease, background 180ms ease;
    }

    .insights-toggle:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(72, 40, 108, 0.12);
        background: linear-gradient(135deg, rgba(255, 152, 0, 0.18), rgba(120, 101, 255, 0.18));
    }

    .sidebar-link-button {
        width: 100%;
        cursor: pointer;
        font: inherit;
        text-align: left;
    }

    .dashboard-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .dashboard-kpi {
        padding: 18px 18px 16px;
        border-radius: 18px;
        border: 1px solid rgba(120, 101, 255, 0.12);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 247, 241, 0.94));
        box-shadow: 0 10px 22px rgba(72, 40, 108, 0.08);
        transition: transform 180ms ease, box-shadow 180ms ease;
    }

    .dashboard-kpi:hover,
    .board-panel:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 28px rgba(72, 40, 108, 0.1);
    }

    .dashboard-kpi.primary {
        background: linear-gradient(180deg, #ff9a18, #f06a3f 52%, #8658d5 100%);
        color: #fff;
    }

    .dashboard-kpi span {
        display: block;
        font-size: 0.82rem;
        color: inherit;
        opacity: 0.76;
    }

    .dashboard-kpi strong {
        display: block;
        margin-top: 10px;
        font-size: clamp(1.7rem, 2.6vw, 2.3rem);
        line-height: 1;
    }

    .dashboard-kpi small {
        display: block;
        margin-top: 10px;
        color: inherit;
        opacity: 0.7;
    }

    .board-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) 260px;
        gap: 18px;
    }

    .board-stack {
        display: grid;
        gap: 18px;
        min-width: 0;
    }

    .board-panel {
        padding: 18px;
        border-radius: 18px;
        border: 1px solid rgba(120, 101, 255, 0.12);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(255, 250, 246, 0.94));
        box-shadow: 0 10px 24px rgba(72, 40, 108, 0.08);
        min-width: 0;
        transition: transform 180ms ease, box-shadow 180ms ease;
    }

    .board-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
    }

    .board-panel-header h2 {
        font-size: 1.08rem;
        letter-spacing: -0.02em;
        color: #1e293b;
    }

    .board-panel-header p {
        margin: 4px 0 0;
        color: rgba(30, 41, 59, 0.62);
        font-size: 0.86rem;
    }

    .board-panel canvas {
        width: 100% !important;
        height: 210px !important;
    }

    .mini-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        background: linear-gradient(135deg, #ff9a18, #f06a3f);
        color: white;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .board-secondary {
        display: grid;
        grid-template-columns: 1.3fr 0.7fr;
        gap: 18px;
    }

    .dashboard-ring-wrap {
        display: grid;
        place-items: center;
        gap: 14px;
        text-align: center;
    }

    .dashboard-ring-wrap canvas {
        height: 170px !important;
        max-width: 170px;
    }

    .alert-list,
    .compact-list {
        display: grid;
        gap: 10px;
    }

    .alert-row,
    .compact-row {
        padding: 10px 12px;
        border-radius: 14px;
        border: 1px solid rgba(120, 101, 255, 0.08);
        background: linear-gradient(135deg, rgba(255, 152, 0, 0.08), rgba(120, 101, 255, 0.08));
    }

    .alert-row strong,
    .compact-row strong {
        display: block;
        font-size: 0.95rem;
        color: #1e293b;
    }

    .alert-row span,
    .compact-row span {
        display: block;
        margin-top: 4px;
        color: rgba(30, 41, 59, 0.62);
        font-size: 0.84rem;
    }

    .board-forms {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .board-forms .board-panel {
        height: 100%;
    }

    .stack-form.compact-form {
        gap: 12px;
    }

    .compact-form input,
    .compact-form select,
    .compact-form textarea {
        min-height: 44px;
        padding: 10px 14px;
        border-radius: 12px;
        background: linear-gradient(180deg, #ffffff, #fff7f0);
    }

    .compact-form textarea {
        min-height: 92px;
    }

    .settings-compact-form {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        align-items: end;
    }

    .settings-compact-form .field {
        margin: 0;
    }

    .settings-compact-form .field:last-of-type {
        grid-column: 1 / -1;
    }

    .settings-submit {
        min-width: 180px;
        justify-self: start;
    }

    .dashboard-main .field input,
    .dashboard-main .field select,
    .dashboard-main .field textarea {
        border: 1px solid rgba(120, 101, 255, 0.12);
        box-shadow: none;
    }

    .dashboard-main .field input:focus,
    .dashboard-main .field select:focus,
    .dashboard-main .field textarea:focus {
        border-color: rgba(120, 101, 255, 0.32);
        box-shadow: 0 0 0 4px rgba(120, 101, 255, 0.08);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        align-items: end;
    }

    .history-toolbar {
        display: grid;
        gap: 14px;
        margin-bottom: 16px;
        padding: 16px;
        border-radius: 16px;
        border: 1px solid rgba(120, 101, 255, 0.1);
        background: linear-gradient(180deg, rgba(255, 247, 240, 0.72), rgba(247, 242, 255, 0.72));
    }

    .history-search-row {
        display: grid;
        grid-template-columns: minmax(0, 1.8fr) repeat(2, minmax(0, 0.75fr)) auto;
        gap: 12px;
        align-items: end;
    }

    .history-search-row .field,
    .filter-grid .field {
        margin: 0;
    }

    .search-input-wrap {
        position: relative;
    }

    .search-input-wrap input {
        padding-left: 44px;
    }

    .search-input-wrap::before {
        content: "";
        position: absolute;
        left: 16px;
        top: 50%;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(44, 33, 81, 0.45);
        border-radius: 999px;
        transform: translateY(-60%);
    }

    .search-input-wrap::after {
        content: "";
        position: absolute;
        left: 29px;
        top: 58%;
        width: 8px;
        height: 2px;
        background: rgba(44, 33, 81, 0.45);
        transform: rotate(45deg);
        border-radius: 999px;
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-summary {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        min-height: 32px;
        padding: 0 12px;
        border-radius: 999px;
        background: rgba(120, 101, 255, 0.08);
        border: 1px solid rgba(120, 101, 255, 0.1);
        color: #4b3d7d;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .history-meta {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
        color: rgba(27, 35, 64, 0.65);
        font-size: 0.9rem;
    }

    .category-action-cell {
        min-width: 460px;
    }

    .category-inline-edit {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(92px, 0.85fr) 84px auto auto;
        gap: 8px;
        align-items: center;
    }

    .settings-grid .category-inline-edit input {
        width: 100%;
        min-width: 0;
        min-height: 40px;
    }

    .settings-grid .category-inline-edit .inline-check {
        gap: 8px;
        white-space: nowrap;
        justify-self: start;
    }

    .settings-grid .category-inline-edit .inline-check input {
        width: 16px;
        min-height: 16px;
    }

    .settings-grid .category-inline-edit .table-action,
    .settings-grid .category-delete-form .danger-button {
        min-height: 40px;
        padding: 0 14px;
        border-radius: 12px;
    }

    .settings-grid .category-delete-form {
        margin-top: 8px;
    }

    .table-panel table {
        min-width: 760px;
    }

    .table-panel tbody tr:hover {
        background: rgba(255, 152, 0, 0.05);
    }

    .goals-grid,
    .settings-grid {
        display: grid;
        grid-template-columns: minmax(280px, 0.82fr) minmax(0, 1.18fr);
        gap: 18px;
    }

    .goal-card {
        padding: 14px;
        border-radius: 16px;
        border: 1px solid rgba(120, 101, 255, 0.08);
        background: linear-gradient(135deg, rgba(255, 152, 0, 0.07), rgba(120, 101, 255, 0.07));
    }

    .goal-card + .goal-card {
        margin-top: 12px;
    }

    .goal-card-header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
    }

    .goal-card-title {
        font-size: 1.16rem;
        font-weight: 800;
        color: #1b2340;
    }

    .goal-card-subtitle {
        margin-top: 4px;
        color: rgba(27, 35, 64, 0.74);
        font-size: 0.92rem;
    }

    .goal-progress-stack {
        margin-top: 12px;
        display: grid;
        gap: 10px;
    }

    .goal-progress-meta {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .goal-progress-value {
        text-align: right;
    }

    .goal-progress-value strong {
        display: block;
        font-size: 1.02rem;
        color: #2d2150;
    }

    .goal-progress-value span {
        display: block;
        margin-top: 2px;
        color: rgba(27, 35, 64, 0.72);
        font-size: 0.84rem;
    }

    .goal-progress-wrap {
        position: relative;
    }

    .goal-progress-wrap .progress-bar {
        margin: 0;
    }

    .goal-progress-wrap .progress-bar.is-complete span {
        background: linear-gradient(135deg, #0f766e, #22c55e);
    }

    .goal-progress-markers {
        position: absolute;
        inset: 0;
        pointer-events: none;
    }

    .goal-progress-markers span {
        position: absolute;
        top: -2px;
        bottom: -2px;
        width: 1px;
        background: rgba(45, 33, 80, 0.12);
    }

    .goal-progress-markers span:nth-child(1) { left: 25%; }
    .goal-progress-markers span:nth-child(2) { left: 50%; }
    .goal-progress-markers span:nth-child(3) { left: 75%; }

    .goal-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-top: 12px;
    }

    .goal-summary-item {
        padding: 10px 12px;
        border-radius: 14px;
        border: 1px solid rgba(120, 101, 255, 0.08);
        background: rgba(255, 255, 255, 0.7);
    }

    .goal-summary-item span {
        display: block;
        color: rgba(27, 35, 64, 0.62);
        font-size: 0.78rem;
    }

    .goal-summary-item strong {
        display: block;
        margin-top: 6px;
        font-size: 0.98rem;
        color: #1b2340;
    }

    .goal-editor {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid rgba(120, 101, 255, 0.08);
    }

    .goal-editor-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        margin-bottom: 12px;
    }

    .goal-editor-header strong {
        font-size: 0.98rem;
    }

    .goal-editor-status {
        color: rgba(27, 35, 64, 0.72);
        font-size: 0.82rem;
        font-weight: 700;
    }

    .goal-editor-grid {
        display: grid;
        gap: 12px;
    }

    .goal-readonly-field {
        min-height: 44px;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid rgba(120, 101, 255, 0.12);
        background: rgba(255, 255, 255, 0.72);
        color: #2d2150;
        font-weight: 700;
    }

    .goal-timeline-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-top: 2px;
    }

    .goal-timeline-item {
        padding: 10px 12px;
        border-radius: 14px;
        border: 1px solid rgba(120, 101, 255, 0.08);
        background: rgba(255, 255, 255, 0.74);
    }

    .goal-timeline-item span {
        display: block;
        color: rgba(27, 35, 64, 0.62);
        font-size: 0.78rem;
    }

    .goal-timeline-item strong {
        display: block;
        margin-top: 6px;
        font-size: 0.94rem;
        color: #1b2340;
    }

    .goal-editor-grid .field {
        margin: 0;
    }

    .goal-editor-grid .two-field-grid {
        gap: 12px;
    }

    .goal-helper-text {
        margin-top: 6px;
        color: rgba(27, 35, 64, 0.78);
        font-size: 0.8rem;
    }

    .goal-warning {
        margin-top: 8px;
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px solid rgba(217, 119, 6, 0.16);
        background: rgba(254, 243, 199, 0.8);
        color: #9a3412;
        font-size: 0.84rem;
    }

    .goal-warning[hidden] {
        display: none;
    }

    .goal-actions {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 12px;
    }

    .goal-update-button {
        min-width: 170px;
    }

    .goal-update-button[disabled] {
        opacity: 0.55;
        cursor: not-allowed;
        transform: none;
    }

    .goal-delete-button {
        min-height: 42px;
        padding: 0 18px;
    }

    .dashboard-empty {
        padding: 28px;
        border-radius: 22px;
        border: 1px solid rgba(120, 101, 255, 0.12);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(255, 248, 242, 0.94));
        box-shadow: 0 16px 28px rgba(72, 40, 108, 0.1);
    }

    .overview-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
    }

    .overview-card {
        padding: 18px;
        border-radius: 20px;
        border: 1px solid rgba(120, 101, 255, 0.12);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 247, 241, 0.94));
        box-shadow: 0 16px 28px rgba(72, 40, 108, 0.1);
    }

    .overview-card span {
        display: block;
        color: rgba(27, 35, 64, 0.62);
        font-size: 0.82rem;
    }

    .overview-card strong {
        display: block;
        margin-top: 10px;
        font-size: 1.7rem;
        color: #1b2340;
    }

    .overview-card small {
        display: block;
        margin-top: 8px;
        color: rgba(27, 35, 64, 0.62);
    }

    .insights-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(24, 18, 44, 0.4);
        backdrop-filter: blur(4px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 220ms ease;
        z-index: 60;
    }

    .insights-drawer {
        position: fixed;
        top: 12px;
        right: 12px;
        bottom: 12px;
        width: min(540px, calc(100vw - 24px));
        padding: 18px;
        border-radius: 28px;
        border: 1px solid rgba(255, 255, 255, 0.24);
        background:
            linear-gradient(180deg, rgba(255, 250, 246, 0.98), rgba(246, 240, 255, 0.98));
        box-shadow: 0 28px 60px rgba(21, 17, 40, 0.24);
        transform: translateX(calc(100% + 20px));
        transition: transform 240ms ease;
        z-index: 70;
        display: grid;
        grid-template-rows: auto 1fr;
        gap: 16px;
    }

    .insights-drawer.is-open,
    .insights-backdrop.is-open {
        opacity: 1;
        pointer-events: auto;
    }

    .insights-drawer.is-open {
        transform: translateX(0);
    }

    .insights-drawer-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .insights-drawer-header h2 {
        margin: 0;
        color: #1b2340;
    }

    .insights-drawer-header p {
        margin: 6px 0 0;
        color: rgba(27, 35, 64, 0.68);
    }

    .insights-drawer-close {
        min-width: 42px;
        min-height: 42px;
        border-radius: 14px;
        border: 1px solid rgba(120, 101, 255, 0.12);
        background: rgba(255, 255, 255, 0.74);
        color: #2c2151;
        font-size: 1.4rem;
        line-height: 1;
        cursor: pointer;
    }

    .insights-drawer-body {
        overflow-y: auto;
        padding-right: 4px;
        display: grid;
        gap: 18px;
    }

    .insights-summary {
        padding: 14px 16px;
        border-radius: 18px;
        border: 1px solid rgba(120, 101, 255, 0.12);
        background: linear-gradient(135deg, rgba(255, 152, 0, 0.09), rgba(120, 101, 255, 0.09));
    }

    .insights-summary strong {
        display: block;
        color: #1b2340;
        font-size: 1rem;
    }

    .insights-summary span {
        display: block;
        margin-top: 6px;
        color: rgba(27, 35, 64, 0.7);
        font-size: 0.92rem;
    }

    .dashboard-main h1,
    .dashboard-main h2,
    .dashboard-main strong {
        color: #1b2340;
    }

    .dashboard-main p,
    .dashboard-main small,
    .dashboard-main .field span {
        color: rgba(27, 35, 64, 0.66);
    }

    .dashboard-main table th {
        color: rgba(27, 35, 64, 0.52);
    }

    .dashboard-main table td {
        color: #25314f;
    }

    @media (max-width: 1180px) {
        .dashboard-shell {
            grid-template-columns: 1fr;
        }

        .dashboard-frame-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .dashboard-frame-nav {
            justify-content: flex-start;
        }

        .dashboard-sidebar {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .dashboard-identity {
            grid-column: 1 / -1;
        }

        .dashboard-kpi-grid,
        .board-forms,
        .goals-grid,
        .settings-grid,
        .overview-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .board-grid,
        .board-secondary {
            grid-template-columns: 1fr;
        }

        .history-search-row,
        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .goal-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .goal-timeline-grid {
            grid-template-columns: 1fr;
        }

        .settings-compact-form {
            grid-template-columns: 1fr;
        }

        .settings-compact-form .field:last-of-type {
            grid-column: auto;
        }

        .category-action-cell {
            min-width: 0;
        }

        .category-inline-edit {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .dashboard-shell-body .shell {
            width: calc(100% - 14px);
        }

        .dashboard-sidebar,
        .dashboard-kpi-grid,
        .board-forms,
        .goals-grid,
        .settings-grid,
        .overview-grid,
        .filter-grid,
        .history-search-row,
        .settings-compact-form {
            grid-template-columns: 1fr;
        }

        .goal-summary-grid,
        .goal-editor-grid .two-field-grid,
        .goal-timeline-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-brand {
            flex-direction: column;
            align-items: flex-start;
        }

        .dashboard-brand-copy strong {
            font-size: 1.2rem;
        }

        .dashboard-main {
            padding: 18px;
        }

        .dashboard-main-header {
            flex-direction: column;
            align-items: flex-start;
            top: 0;
            margin: -2px -2px 0;
            padding-left: 4px;
            padding-right: 4px;
        }

        .category-inline-edit {
            grid-template-columns: 1fr;
        }

        .header-actions {
            justify-content: flex-start;
        }

        .insights-drawer {
            top: 8px;
            right: 8px;
            bottom: 8px;
            width: calc(100vw - 16px);
            border-radius: 22px;
        }
    }
</style>

<div class="dashboard-shell">
    <header class="dashboard-frame-header">
        <div class="dashboard-brand">
            <a href="{{ route('logo.viewer') }}" aria-label="View {{ $brandName }} logo">
                <img class="dashboard-brand-logo" src="{{ $brandLogo }}" alt="{{ $brandName }} logo">
            </a>
            <div class="dashboard-brand-copy">
                <strong>{{ $brandName }}</strong>
                <span>{{ $brandTagline }}</span>
            </div>
        </div>

        <nav class="dashboard-frame-nav">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('csv-import.index') }}">CSV Import</a>
            <a href="#planner">Planner</a>
            <button type="button" class="install-trigger" hidden>Install App</button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </nav>
    </header>

    <aside class="dashboard-sidebar">
        <div class="dashboard-identity">
            <div class="dashboard-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div style="margin-top:18px;">
                <strong>{{ strtoupper($user->name) }}</strong>
                <span>{{ $user->email }}</span>
            </div>
        </div>

        <div>
            <span class="sidebar-section-title">Navigation</span>
            <div class="sidebar-nav">
                <a class="sidebar-link active dashboard-nav-link" href="#overview" data-target="overview">Overview <code>Home</code></a>
                <a class="sidebar-link dashboard-nav-link" href="#planner" data-target="planner">Planner <code>Cycle</code></a>
                <a class="sidebar-link dashboard-nav-link" href="#history" data-target="history">History <code>Logs</code></a>
                <a class="sidebar-link dashboard-nav-link" href="#goals" data-target="goals">Goals <code>Save</code></a>
                <a class="sidebar-link dashboard-nav-link" href="#settings" data-target="settings">Settings <code>Edit</code></a>
                <button type="button" class="sidebar-link sidebar-link-button insights-toggle" data-insights-open>
                    Reports <code>Stats</code>
                </button>
            </div>
        </div>

        <div>
            <span class="sidebar-section-title">Snapshot</span>
            <div class="sidebar-mini-stats">
                <div class="sidebar-mini-card">
                    <span>Currency</span>
                    <strong>{{ $currency }}</strong>
                </div>
                <div class="sidebar-mini-card">
                    <span>Categories</span>
                    <strong>{{ $categories->count() }}</strong>
                </div>
                <div class="sidebar-mini-card">
                    <span>Goals</span>
                    <strong>{{ $savingsGoals->count() }}</strong>
                </div>
            </div>
        </div>
    </aside>

    <section class="dashboard-main">
        <div class="dashboard-main-header">
            <div>
                <h1>Dashboard User</h1>
                <p>Budget tracking, reports, categories, alerts, and savings controls in one admin board.</p>
            </div>

            <div class="header-actions">
                <form method="GET" action="{{ route('dashboard') }}" class="cycle-switcher">
                    <label class="field compact-field">
                        <span>View cycle</span>
                        <select name="cycle" onchange="this.form.submit()">
                            <option value="">Current active cycle</option>
                            @foreach($cycles as $item)
                                <option value="{{ $item->id }}" {{ $cycle && $cycle->id === $item->id ? 'selected' : '' }}>
                                    {{ $item->start_date->format('M d') }} - {{ $item->end_date->format('M d') }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </form>
                <button type="button" class="insights-toggle" data-insights-open>Open Reports</button>
                <a href="{{ route('dashboard.backup') }}" class="secondary-button">Download Backup</a>
                <a href="{{ route('csv-import.index') }}" class="secondary-button">CSV Import</a>
            </div>
        </div>

        <section class="dashboard-panel-section is-active" id="overview">
            @if($cycle && $summary)
                <div class="overview-grid">
                    <article class="overview-card">
                        <span>Active cycle</span>
                        <strong>{{ $cycle->start_date->format('M d') }} - {{ $cycle->end_date->format('M d') }}</strong>
                        <small>{{ number_format($cycle->amount, 2) }} planned income</small>
                    </article>
                    <article class="overview-card">
                        <span>Transactions</span>
                        <strong>{{ $filteredTransactions->count() }}</strong>
                        <small>Visible records in the selected cycle</small>
                    </article>
                    <article class="overview-card">
                        <span>Categories</span>
                        <strong>{{ $categories->count() }}</strong>
                        <small>Budget groups and bill categories ready</small>
                    </article>
                    <article class="overview-card">
                        <span>Alerts</span>
                        <strong>{{ ($budgetAlerts->count() ?? 0) + ($dueBillAlerts->count() ?? 0) + (!empty($summary['warning']) ? 1 : 0) }}</strong>
                        <small>Open Reports for charts, alerts, and statistics</small>
                    </article>
                </div>

                <article class="dashboard-empty">
                    <span class="eyebrow">Overview</span>
                    <h2 style="margin-top:14px;">Each section is now separated inside the dashboard.</h2>
                    <p>Use the sidebar to switch between Planner, History, Goals, and Settings. Open the Reports button when you want charts, alerts, and statistics without making the page too long.</p>
                </article>
            @else
                <div class="dashboard-empty">
                    <span class="eyebrow">Start Here</span>
                    <h2 style="margin-top:14px;">No cycle exists yet.</h2>
                    <p>Create your first income cycle in Planner to unlock transaction tracking, reports, alerts, goals, and the rest of the budget tools.</p>
                </div>
            @endif
        </section>

        <section class="dashboard-panel-section" id="planner">
            <div class="board-forms">
            <article class="board-panel">
                <div class="board-panel-header">
                    <div>
                        <h2>Create Income Cycle</h2>
                        <p>Set the main pay period for reporting and unlock the full dashboard.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('income-cycles.store') }}" class="stack-form compact-form">
                    @csrf
                    <label class="field">
                        <span>Income amount</span>
                        <input type="number" step="0.01" name="amount" placeholder="6000.00" required>
                    </label>
                    <label class="field">
                        <span>Start date</span>
                        <input type="date" name="start_date" required>
                    </label>
                    <label class="field">
                        <span>End date</span>
                        <input type="date" name="end_date" required>
                    </label>
                    <button type="submit" class="primary-button full-width">Create Cycle</button>
                </form>
            </article>

            @if($cycle && $summary)
                <article class="board-panel">
                    <div class="board-panel-header">
                        <div>
                            <h2>Add income or expense</h2>
                            <p>Track every entry from one compact form.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('transactions.store') }}" class="stack-form compact-form offline-transaction-form" data-sync-url="{{ route('transactions.sync') }}">
                        @csrf
                        <input type="hidden" name="cycle_id" value="{{ $cycle->id }}">
                        <label class="field">
                            <span>Entry type</span>
                            <select name="transaction_type" class="transaction-type-select" required>
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>
                        </label>
                        <label class="field transaction-category-field">
                            <span>Category</span>
                            <select name="category_id">
                                <option value="">Select category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="field">
                            <span>Amount</span>
                            <input type="number" step="0.01" name="amount" placeholder="65.20" required>
                        </label>
                        <label class="field">
                            <span>Timestamp</span>
                            <input type="datetime-local" name="timestamp" value="{{ now()->format('Y-m-d\TH:i') }}">
                        </label>
                        <label class="field">
                            <span>Note</span>
                            <textarea name="note" placeholder="Salary, groceries, rent, tuition, fuel."></textarea>
                        </label>
                        <button type="submit" class="primary-button full-width">Save Entry</button>
                    </form>
                </article>
            @else
                <article class="board-panel">
                    <div class="board-panel-header">
                        <div>
                            <h2>Next Step</h2>
                            <p>Create your first cycle first, then income, expenses, charts, and reports will appear here.</p>
                        </div>
                    </div>
                    <div class="compact-list">
                        <div class="compact-row">
                            <strong>1. Create an income cycle</strong>
                            <span>Set amount, start date, and end date.</span>
                        </div>
                        <div class="compact-row">
                            <strong>2. Add categories and entries</strong>
                            <span>Once a cycle exists, transaction forms and tracking tools become available.</span>
                        </div>
                        <div class="compact-row">
                            <strong>3. View reports and alerts</strong>
                            <span>The dashboard overview will unlock automatically after creation.</span>
                        </div>
                    </div>
                </article>
            @endif

            <article class="board-panel">
                <div class="board-panel-header">
                    <div>
                        <h2>Category + Budget Setup</h2>
                        <p>Maintain limits and due dates.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('categories.store') }}" class="stack-form compact-form">
                    @csrf
                    <label class="field">
                        <span>Category name</span>
                        <input type="text" name="name" placeholder="Food, Rent, Bills" required>
                    </label>
                    <label class="field">
                        <span>Budget limit</span>
                        <input type="number" step="0.01" name="budget_limit" placeholder="800.00">
                    </label>
                    <label class="field">
                        <span>Bill due day</span>
                        <input type="number" min="1" max="31" name="due_day" placeholder="15">
                    </label>
                    <label class="inline-check">
                        <input type="checkbox" name="is_fixed" value="1">
                        <span>Fixed expense / bill</span>
                    </label>
                    <button type="submit" class="secondary-button full-width">Create Category</button>
                </form>
            </article>
            </div>

        @if($cycle && $summary)
            <article class="board-panel">
                <div class="board-panel-header">
                    <div>
                        <h2>Income Cycle Management</h2>
                        <p>Full CRUD for your budgeting cycles.</p>
                    </div>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Amount</th>
                                <th>Transactions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cycles as $item)
                                <tr>
                                    <td>
                                        {{ $item->start_date->format('M d, Y') }} - {{ $item->end_date->format('M d, Y') }}
                                    </td>
                                    <td>{{ number_format($item->amount, 2) }}</td>
                                    <td>{{ $item->transactions_count }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('income-cycles.update', $item) }}" class="inline-edit-grid">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" step="0.01" name="amount" value="{{ $item->amount }}" required>
                                            <input type="date" name="start_date" value="{{ $item->start_date->format('Y-m-d') }}" required>
                                            <input type="date" name="end_date" value="{{ $item->end_date->format('Y-m-d') }}" required>
                                            <button type="submit" class="table-action">Update</button>
                                        </form>
                                        <form method="POST" action="{{ route('income-cycles.destroy', $item) }}" onsubmit="return confirm('Delete this income cycle?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="danger-button">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No income cycles yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        @endif
        </section>

        <section class="dashboard-panel-section" id="history">
            @if($cycle && $summary)
            <article class="board-panel table-panel">
                <div class="board-panel-header">
                    <div>
                        <h2>Transaction history</h2>
                        <p>Search and filter full income and expense records.</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('dashboard') }}" class="history-toolbar">
                    <input type="hidden" name="cycle" value="{{ $cycle->id }}">

                    <div class="history-search-row">
                        <label class="field search-input-wrap">
                            <span>Search transactions</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search note, category, or description">
                        </label>
                        <label class="field">
                            <span>Type</span>
                            <select name="transaction_type">
                                <option value="">All</option>
                                <option value="expense" {{ request('transaction_type') === 'expense' ? 'selected' : '' }}>Expense</option>
                                <option value="income" {{ request('transaction_type') === 'income' ? 'selected' : '' }}>Income</option>
                            </select>
                        </label>
                        <label class="field">
                            <span>Category</span>
                            <select name="category_id">
                                <option value="">All</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <div class="filter-actions">
                            <button type="submit" class="secondary-button">Apply</button>
                            <a href="{{ route('dashboard', ['cycle' => $cycle->id]) }}#history" class="secondary-button">Reset</a>
                        </div>
                    </div>

                    <div class="filter-grid">
                        <label class="field">
                            <span>From</span>
                            <input type="date" name="date_from" value="{{ request('date_from') }}">
                        </label>
                        <label class="field">
                            <span>To</span>
                            <input type="date" name="date_to" value="{{ request('date_to') }}">
                        </label>
                        <label class="field">
                            <span>Min amount</span>
                            <input type="number" step="0.01" name="amount_min" value="{{ request('amount_min') }}">
                        </label>
                        <label class="field">
                            <span>Max amount</span>
                            <input type="number" step="0.01" name="amount_max" value="{{ request('amount_max') }}">
                        </label>
                    </div>

                    <div class="history-meta">
                        <span>{{ $filteredTransactions->count() }} result{{ $filteredTransactions->count() === 1 ? '' : 's' }} found in this cycle.</span>
                        <div class="filter-summary">
                            @if(request('search'))
                                <span class="filter-chip">Search: {{ request('search') }}</span>
                            @endif
                            @if(request('transaction_type'))
                                <span class="filter-chip">Type: {{ ucfirst(request('transaction_type')) }}</span>
                            @endif
                            @if(request('category_id'))
                                <span class="filter-chip">Category: {{ optional($categories->firstWhere('id', (int) request('category_id')))->name ?? 'Selected' }}</span>
                            @endif
                            @if(request('date_from') || request('date_to'))
                                <span class="filter-chip">Date: {{ request('date_from') ?: 'Any' }} to {{ request('date_to') ?: 'Any' }}</span>
                            @endif
                            @if(request('amount_min') || request('amount_max'))
                                <span class="filter-chip">Amount: {{ request('amount_min') ?: '0' }} to {{ request('amount_max') ?: 'Any' }}</span>
                            @endif
                            @if(! request('search') && ! request('transaction_type') && ! request('category_id') && ! request('date_from') && ! request('date_to') && ! request('amount_min') && ! request('amount_max'))
                                <span class="filter-chip">No active filters</span>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($filteredTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->timestamp?->format('M d, Y H:i') }}</td>
                                    <td>{{ ucfirst($transaction->transaction_type) }}</td>
                                    <td>{{ $transaction->category?->name ?? 'General income' }}</td>
                                    <td>{{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ $transaction->note }}</td>
                                    <td class="action-row">
                                        <a class="table-action" href="{{ route('transactions.edit', $transaction) }}">Edit</a>
                                        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Delete this transaction?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="danger-button">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No matching transactions found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
            @else
            <article class="dashboard-empty">
                <span class="eyebrow">History</span>
                <h2 style="margin-top:14px;">Transaction history will appear here.</h2>
                <p>Create a cycle and save at least one income or expense entry in Planner to start using filters and full history logs.</p>
            </article>
            @endif
        </section>

        <section class="dashboard-panel-section" id="goals">
            <div class="goals-grid">
                <article class="board-panel">
                    <div class="board-panel-header">
                        <div>
                            <h2>Create Savings Goal</h2>
                            <p>Emergency fund, travel, school fees, and more.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('savings-goals.store') }}" class="stack-form compact-form">
                        @csrf
                        <label class="field">
                            <span>Goal name</span>
                            <input type="text" name="name" placeholder="Emergency fund" required>
                        </label>
                        <label class="field">
                            <span>Target amount</span>
                            <input type="number" step="0.01" min="0.01" inputmode="decimal" name="target_amount" placeholder="{{ $currencySymbol }}1,000.00" required>
                        </label>
                        <label class="field">
                            <span>Current saved</span>
                            <input type="number" step="0.01" min="0" inputmode="decimal" name="current_amount" value="0">
                        </label>
                        <label class="field">
                            <span>Target date</span>
                            <input type="date" name="target_date">
                        </label>
                        <label class="field">
                            <span>Notes</span>
                            <textarea name="notes" placeholder="Anything important about this goal."></textarea>
                        </label>
                        <button type="submit" class="primary-button full-width">Add Goal</button>
                    </form>
                </article>

                <article class="board-panel">
                    <div class="board-panel-header">
                        <div>
                            <h2>Edit Goal</h2>
                            <p>Savings progress is shown first, then you can update the selected goal with clearer fields and safer actions.</p>
                        </div>
                    </div>
                    @forelse($savingsGoals as $goal)
                        @php
                            $goalProgress = min(($goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0), 100);
                            $goalRemaining = max($goal->target_amount - $goal->current_amount, 0);
                            $daysLeft = $goal->target_date ? now()->startOfDay()->diffInDays($goal->target_date->startOfDay(), false) : null;
                            $deadlineStatus = $goal->target_date
                                ? ($daysLeft < 0
                                    ? 'Past due by '.abs($daysLeft).' day'.(abs($daysLeft) === 1 ? '' : 's')
                                    : ($daysLeft === 0
                                        ? 'Due today'
                                        : $daysLeft.' day'.($daysLeft === 1 ? '' : 's').' left'))
                                : 'No deadline set';
                            $monthsLeft = $goal->target_date
                                ? max(now()->startOfDay()->diffInMonths($goal->target_date->startOfDay(), false), 1)
                                : null;
                            $suggestedMonthly = $goalRemaining > 0
                                ? ($monthsLeft ? $goalRemaining / $monthsLeft : $goalRemaining / 3)
                                : 0;
                            $trackedSince = $goal->created_at?->format('M d, Y h:i A') ?? 'Not available';
                            $lastUpdated = $goal->updated_at?->format('M d, Y h:i A') ?? 'Not available';
                        @endphp
                        <div class="goal-card">
                            <div class="goal-card-header">
                                <div>
                                    <div class="goal-card-title">{{ $goal->name }}</div>
                                    <div class="goal-card-subtitle">{{ $goal->target_date ? 'Target '.$goal->target_date->format('M d, Y') : 'No deadline set' }}</div>
                                </div>
                                <div class="goal-progress-value">
                                    <strong>{{ $currencySymbol }}{{ number_format($goal->current_amount, 2) }} / {{ $currencySymbol }}{{ number_format($goal->target_amount, 2) }}</strong>
                                    <span>{{ number_format($goalProgress, 2) }}% saved</span>
                                </div>
                            </div>

                            <div class="goal-progress-stack">
                                <div class="goal-progress-meta">
                                    <small>{{ $deadlineStatus }}</small>
                                    <small>{{ $goal->is_completed ? 'Completed' : 'In progress' }}</small>
                                </div>
                                <div class="goal-progress-wrap">
                                    <div class="progress-bar {{ $goal->is_completed ? 'is-complete' : '' }}">
                                        <span style="width: {{ $goalProgress }}%"></span>
                                    </div>
                                    <div class="goal-progress-markers" aria-hidden="true">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="goal-summary-grid">
                                <div class="goal-summary-item">
                                    <span>Remaining</span>
                                    <strong>{{ $currencySymbol }}{{ number_format($goalRemaining, 2) }}</strong>
                                </div>
                                <div class="goal-summary-item">
                                    <span>Saved</span>
                                    <strong>{{ number_format($goalProgress, 2) }}%</strong>
                                </div>
                                <div class="goal-summary-item">
                                    <span>Deadline</span>
                                    <strong>{{ $deadlineStatus }}</strong>
                                </div>
                                <div class="goal-summary-item">
                                    <span>Suggested monthly</span>
                                    <strong>{{ $currencySymbol }}{{ number_format($suggestedMonthly, 2) }}/month</strong>
                                </div>
                            </div>

                            <div class="goal-editor">
                                <div class="goal-editor-header">
                                    <strong>Selected Goal Details</strong>
                                    <span class="goal-editor-status">Changes update the live summary below.</span>
                                </div>

                            <form method="POST"
                                action="{{ route('savings-goals.update', $goal) }}"
                                class="stack-form compact-form goal-editor-grid"
                                data-goal-editor
                                data-currency-symbol="{{ $currencySymbol }}"
                                data-goal-name="{{ $goal->name }}"
                                data-goal-created-at="{{ $goal->created_at?->toIso8601String() }}"
                                data-goal-updated-at="{{ $goal->updated_at?->toIso8601String() }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="name" value="{{ $goal->name }}">

                                <label class="field">
                                    <span>Goal name</span>
                                    <div class="goal-readonly-field">{{ $goal->name }}</div>
                                    <small class="goal-helper-text">Goal name is locked after creation to keep history consistent.</small>
                                </label>

                                <div class="two-field-grid">
                                    <label class="field">
                                        <span>Target amount</span>
                                        <input type="number" step="0.01" min="0.01" inputmode="decimal" name="target_amount" value="{{ number_format($goal->target_amount, 2, '.', '') }}" required>
                                    </label>
                                    <label class="field">
                                        <span>Current saved</span>
                                        <input type="number" step="0.01" min="0" inputmode="decimal" name="current_amount" value="{{ number_format($goal->current_amount, 2, '.', '') }}">
                                    </label>
                                </div>

                                <label class="field">
                                    <span>Target date</span>
                                    <input type="date" name="target_date" value="{{ $goal->target_date?->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}">
                                    <small class="goal-helper-text" data-deadline-text>{{ $deadlineStatus }}</small>
                                </label>

                                <div class="goal-timeline-grid">
                                    <div class="goal-timeline-item">
                                        <span>Started tracking</span>
                                        <strong data-goal-created-text>{{ $trackedSince }}</strong>
                                    </div>
                                    <div class="goal-timeline-item">
                                        <span>Last savings update</span>
                                        <strong data-goal-updated-text>{{ $lastUpdated }}</strong>
                                    </div>
                                    <div class="goal-timeline-item">
                                        <span>Time until target</span>
                                        <strong data-goal-live-timer>{{ $deadlineStatus }}</strong>
                                    </div>
                                </div>

                                <label class="field">
                                    <span>Notes</span>
                                    <textarea name="notes">{{ $goal->notes }}</textarea>
                                </label>

                                <div class="goal-warning" data-goal-warning hidden></div>
                                <div class="goal-summary-grid" data-live-summary>
                                    <div class="goal-summary-item">
                                        <span>Remaining</span>
                                        <strong data-summary-remaining>{{ $currencySymbol }}{{ number_format($goalRemaining, 2) }}</strong>
                                    </div>
                                    <div class="goal-summary-item">
                                        <span>Saved</span>
                                        <strong data-summary-saved>{{ number_format($goalProgress, 2) }}%</strong>
                                    </div>
                                    <div class="goal-summary-item">
                                        <span>Status</span>
                                        <strong data-summary-status>{{ $goal->is_completed ? 'Completed' : 'In progress' }}</strong>
                                    </div>
                                    <div class="goal-summary-item">
                                        <span>Suggested monthly</span>
                                        <strong data-summary-monthly>{{ $currencySymbol }}{{ number_format($suggestedMonthly, 2) }}/month</strong>
                                    </div>
                                </div>

                                <div class="goal-actions">
                                    <button type="submit" class="primary-button goal-update-button" data-goal-submit>Update Goal</button>
                                </div>
                            </form>
                            </div>

                            <form method="POST" action="{{ route('savings-goals.destroy', $goal) }}" class="category-delete-form" data-delete-confirm data-delete-label="{{ $goal->name }}" style="margin-top:14px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="danger-button goal-delete-button">Delete Goal</button>
                            </form>
                        </div>
                    @empty
                        <p class="soft-note">No savings goals yet. Create one to start tracking progress.</p>
                    @endforelse
                </article>
            </div>
        </section>

        <section class="dashboard-panel-section" id="settings">
            <div class="settings-grid">
                <article class="board-panel">
                    <div class="board-panel-header">
                        <div>
                            <h2>Account Budget Settings</h2>
                            <p>Set currency, monthly limits, and savings target.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('dashboard.settings.update') }}" class="stack-form compact-form settings-compact-form">
                        @csrf
                        <label class="field">
                            <span>Currency</span>
                            <input type="text" name="currency_pref" value="{{ old('currency_pref', $user->currency_pref) }}" maxlength="10" required>
                        </label>
                        <label class="field">
                            <span>Savings goal %</span>
                            <input type="number" step="0.01" min="0" max="100" name="savings_goal_percentage" value="{{ old('savings_goal_percentage', $user->savings_goal_percentage) }}" required>
                        </label>
                        <label class="field">
                            <span>Monthly budget limit</span>
                            <input type="number" step="0.01" min="0" name="monthly_budget_limit" value="{{ old('monthly_budget_limit', $user->monthly_budget_limit) }}" placeholder="5000.00">
                        </label>
                        <button type="submit" class="secondary-button settings-submit">Save Settings</button>
                    </form>
                </article>

                <article class="board-panel">
                    <div class="board-panel-header">
                        <div>
                            <h2>Category Settings</h2>
                            <p>Edit budgets, bill days, and category types.</p>
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Budget</th>
                                    <th>Due</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $category->name }}<br><small>{{ $category->is_fixed ? 'Fixed' : 'Variable' }}</small></td>
                                        <td>{{ $category->budget_limit ? number_format($category->budget_limit, 2) : 'Not set' }}</td>
                                        <td>{{ $category->due_day ?: 'N/A' }}</td>
                                        <td class="category-action-cell">
                                            <form method="POST" action="{{ route('categories.update', $category) }}" class="inline-edit-grid category-inline-edit">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" value="{{ $category->name }}" required>
                                                <input type="number" step="0.01" name="budget_limit" value="{{ $category->budget_limit }}">
                                                <input type="number" min="1" max="31" name="due_day" value="{{ $category->due_day }}">
                                                <label class="inline-check">
                                                    <input type="checkbox" name="is_fixed" value="1" {{ $category->is_fixed ? 'checked' : '' }}>
                                                    <span>Fixed</span>
                                                </label>
                                                <button type="submit" class="table-action">Update</button>
                                            </form>
                                            <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?');" class="category-delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="danger-button">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">No categories yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
        </section>
    </section>
</div>

@if($cycle && $summary)
    <div class="insights-backdrop" data-insights-backdrop></div>
    <aside class="insights-drawer" data-insights-drawer aria-hidden="true">
        <div class="insights-drawer-header">
            <div>
                <h2>Statistics and Reports</h2>
                <p>Open this side panel anytime to review totals, charts, alerts, and savings performance.</p>
            </div>
            <button type="button" class="insights-drawer-close" aria-label="Close reports panel" data-insights-close>&times;</button>
        </div>

        <div class="insights-drawer-body">
            <div class="insights-summary">
                <strong>Reports are now in one side panel.</strong>
                <span>Use the Open Reports button so the main dashboard stays shorter and easier to navigate.</span>
            </div>

            <div class="dashboard-kpi-grid">
                <article class="dashboard-kpi primary">
                    <span>Earnings</span>
                    <strong>{{ $currency }} {{ number_format($summary['totalIncome'], 0) }}</strong>
                    <small>Cycle + extra income</small>
                </article>
                <article class="dashboard-kpi">
                    <span>Expenses</span>
                    <strong>{{ number_format($summary['totalExpenses'], 0) }}</strong>
                    <small>{{ number_format($summary['spendProgress'], 2) }}% of budget</small>
                </article>
                <article class="dashboard-kpi">
                    <span>Balance</span>
                    <strong>{{ number_format($summary['remainingBalance'], 0) }}</strong>
                    <small>Remaining after spending</small>
                </article>
                <article class="dashboard-kpi">
                    <span>Savings</span>
                    <strong>{{ $summary['savingsProgress'] !== null ? number_format(min($summary['savingsProgress'], 100), 0) : 0 }}%</strong>
                    <small>Goal progress</small>
                </article>
            </div>

            <div class="board-grid">
                <div class="board-stack">
                    <article class="board-panel">
                        <div class="board-panel-header">
                            <div>
                                <h2>Budget vs Actual</h2>
                                <p>Monthly performance across your live categories.</p>
                            </div>
                            <span class="mini-action">Check Now</span>
                        </div>
                        <canvas id="budgetActualChart"
                            data-chart-type="bar"
                            data-labels='@json($summary["budgetVsActualLabels"])'
                            data-budget='@json($summary["budgetVsActualBudget"])'
                            data-spent='@json($summary["budgetVsActualSpent"])'></canvas>
                    </article>

                    <div class="board-secondary">
                        <article class="board-panel">
                            <div class="board-panel-header">
                                <div>
                                    <h2>Income vs Expenses Trend</h2>
                                    <p>Daily movement inside the selected cycle.</p>
                                </div>
                            </div>
                            <canvas id="trendChart"
                                data-chart-type="line"
                                data-labels='@json($summary["trendLabels"])'
                                data-income='@json($summary["trendIncome"])'
                                data-expenses='@json($summary["trendExpenses"])'></canvas>
                        </article>

                        <article class="board-panel">
                            <div class="board-panel-header">
                                <div>
                                    <h2>Quick Alerts</h2>
                                    <p>Overspending and bill reminders.</p>
                                </div>
                            </div>
                            <div class="alert-list">
                                @if($summary['warning'])
                                    <div class="alert-row notification-source" data-notification-title="Budget Warning" data-notification-body="{{ $summary['warning'] }}">
                                        <strong>Burn rate alert</strong>
                                        <span>{{ $summary['warning'] }}</span>
                                    </div>
                                @endif

                                @forelse($budgetAlerts->take(2) as $alert)
                                    <div class="alert-row notification-source" data-notification-title="Overspending Alert" data-notification-body="{{ $alert['name'] }} is {{ $alert['used_percentage'] }}% used.">
                                        <strong>{{ $alert['name'] }}</strong>
                                        <span>{{ $alert['used_percentage'] }}% used with {{ number_format($alert['remaining'], 2) }} left.</span>
                                    </div>
                                @empty
                                    @if($dueBillAlerts->isEmpty() && ! $summary['warning'])
                                        <div class="alert-row">
                                            <strong>No urgent alerts</strong>
                                            <span>Your current cycle is within expected range.</span>
                                        </div>
                                    @endif
                                @endforelse

                                @foreach($dueBillAlerts->take(2) as $alert)
                                    <div class="alert-row notification-source" data-notification-title="Bill Due Reminder" data-notification-body="{{ $alert['name'] }} is due in {{ $alert['days_until_due'] }} day(s).">
                                        <strong>{{ $alert['name'] }}</strong>
                                        <span>Due in {{ $alert['days_until_due'] }} day{{ $alert['days_until_due'] === 1 ? '' : 's' }}.</span>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    </div>
                </div>

                <article class="board-panel">
                    <div class="dashboard-ring-wrap">
                        <div>
                            <h2 style="margin:0;color:#1e293b;">Savings Rating</h2>
                            <p style="margin:6px 0 0;color:rgba(30,41,59,0.62);">Current cycle target progress</p>
                        </div>
                        <canvas id="categoryChart"
                            data-chart-type="doughnut"
                            data-labels='@json(["Saved", "Remaining"])'
                            data-values='@json($savingsRingValues)'></canvas>
                        <strong style="font-size:2rem;color:#1e293b;">{{ $summary['savingsProgress'] !== null ? number_format(min($summary['savingsProgress'], 100), 0) : 0 }}%</strong>
                        <div class="compact-list" style="width:100%;">
                            <div class="compact-row">
                                <strong>Monthly limit</strong>
                                <span>{{ $summary['monthlyLimit'] ? number_format($summary['monthlyLimit'], 2) : 'Not set' }}</span>
                            </div>
                            <div class="compact-row">
                                <strong>Used</strong>
                                <span>{{ $summary['monthlyBudgetUsed'] !== null ? number_format($summary['monthlyBudgetUsed'], 2).'%' : 'N/A' }}</span>
                            </div>
                            <div class="compact-row">
                                <strong>Burn rate</strong>
                                <span>{{ number_format($summary['burnRate'], 2) }} / day</span>
                            </div>
                        </div>
                        <a href="#settings" class="mini-action" data-insights-close>Check Now</a>
                    </div>
                </article>
            </div>
        </div>
    </aside>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dashboardPanelStorageKey = 'budget.activeDashboardPanel';
        const navLinks = [...document.querySelectorAll('.dashboard-nav-link')];
        const panelSections = [...document.querySelectorAll('.dashboard-panel-section')];
        const insightsDrawer = document.querySelector('[data-insights-drawer]');
        const insightsBackdrop = document.querySelector('[data-insights-backdrop]');
        const insightsOpenButtons = [...document.querySelectorAll('[data-insights-open]')];
        const insightsCloseButtons = [...document.querySelectorAll('[data-insights-close]')];

        const activatePanel = (targetId, updateHash = true) => {
            const targetPanel = document.getElementById(targetId);

            if (!targetPanel) {
                return;
            }

            panelSections.forEach((section) => {
                section.classList.toggle('is-active', section.id === targetId);
            });

            navLinks.forEach((link) => {
                link.classList.toggle('active', link.dataset.target === targetId);
            });

            try {
                window.sessionStorage.setItem(dashboardPanelStorageKey, targetId);
            } catch (error) {
                console.debug('Unable to store active dashboard panel.', error);
            }

            if (updateHash) {
                window.history.replaceState(null, '', `#${targetId}`);
            }
        };

        const setInsightsState = (isOpen) => {
            if (!insightsDrawer || !insightsBackdrop) {
                return;
            }

            insightsDrawer.classList.toggle('is-open', isOpen);
            insightsBackdrop.classList.toggle('is-open', isOpen);
            insightsDrawer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            document.body.style.overflow = isOpen ? 'hidden' : '';
        };

        navLinks.forEach((link) => {
            link.addEventListener('click', (event) => {
                const targetId = link.dataset.target;

                if (!targetId) {
                    return;
                }

                event.preventDefault();
                activatePanel(targetId);
            });
        });

        insightsOpenButtons.forEach((button) => {
            button.addEventListener('click', () => setInsightsState(true));
        });

        insightsCloseButtons.forEach((button) => {
            button.addEventListener('click', () => setInsightsState(false));
        });

        if (insightsBackdrop) {
            insightsBackdrop.addEventListener('click', () => setInsightsState(false));
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setInsightsState(false);
            }
        });

        if (!panelSections.length) {
            return;
        }

        let storedTarget = '';
        try {
            storedTarget = window.sessionStorage.getItem(dashboardPanelStorageKey) || '';
        } catch (error) {
            storedTarget = '';
        }

        const initialTarget = window.location.hash.replace('#', '') || storedTarget || 'overview';
        activatePanel(initialTarget, false);
    });
</script>
@endpush
