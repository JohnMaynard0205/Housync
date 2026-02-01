@extends('layouts.landlord-app')

@section('title', 'Payments')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/billing.css') }}">
@endpush

@section('content')
    <div class="billing-content">
        <div class="page-header">
            <div class="page-title-section">
                <h2>Payments &amp; Billing</h2>
                <p>Track rent and utility bills across your units.</p>
            </div>
            <div class="page-actions">
                <div class="date-filter">
                    <i class="fas fa-filter"></i>
                    <span>Filters are basic for now</span>
                </div>
            </div>
        </div>

        <div class="financial-summary">
            <div class="summary-card revenue">
                <div class="summary-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="summary-info">
                    <h3>Total Billed</h3>
                    <span class="summary-value">₱{{ number_format($summary['total_amount'] ?? 0, 2) }}</span>
                </div>
            </div>
            <div class="summary-card collected">
                <div class="summary-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="summary-info">
                    <h3>Collected</h3>
                    <span class="summary-value">₱{{ number_format($summary['total_collected'] ?? 0, 2) }}</span>
                </div>
            </div>
            <div class="summary-card outstanding">
                <div class="summary-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="summary-info">
                    <h3>Outstanding</h3>
                    <span class="summary-value">₱{{ number_format($summary['total_outstanding'] ?? 0, 2) }}</span>
                </div>
            </div>
            <div class="summary-card pending">
                <div class="summary-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="summary-info">
                    <h3>Unpaid / Partial Bills</h3>
                    <span class="summary-value">{{ $summary['pending_count'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="billing-main">
            <div class="payments-section">
                <div class="section-header">
                    <h3>Recent Bills</h3>
                </div>
                <div class="payments-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Tenant</th>
                                <th>Unit</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($bills as $bill)
                            <tr>
                                <td class="invoice-number">{{ $bill->invoice_number }}</td>
                                <td>{{ optional($bill->tenant)->name ?? '—' }}</td>
                                <td>
                                    @if($bill->unit)
                                        {{ $bill->unit->unit_number }} ({{ $bill->unit->property->name ?? 'Property' }})
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ ucfirst($bill->type) }}</td>
                                <td class="amount">₱{{ number_format($bill->amount, 2) }}</td>
                                <td class="amount">₱{{ number_format($bill->balance, 2) }}</td>
                                <td>
                                    <span class="status {{ $bill->status }}">
                                        {{ str_replace('_', ' ', ucfirst($bill->status)) }}
                                    </span>
                                </td>
                                <td>{{ $bill->due_date ? $bill->due_date->format('M d, Y') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center; padding: 24px;">
                                    No bills created yet. You can start by adding billing to your units in a future update.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($bills, 'links'))
                    <div class="mt-3 px-4">
                        {{ $bills->links() }}
                    </div>
                @endif
            </div>

            <aside class="billing-sidebar">
                <div class="quick-actions">
                    <h4>Quick Notes</h4>
                    <div class="action-buttons">
                        <p style="font-size: 0.9rem; color: #64748b; margin: 0;">
                            This Payments page is currently a read-only overview. You can extend it later with bill creation and payment recording.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection



