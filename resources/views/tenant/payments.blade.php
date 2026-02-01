@extends('layouts.app')

@section('title', 'Payments')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/billing.css') }}">
@endpush

@section('content')
    <div class="billing-content">
        <div class="page-header">
            <div class="page-title-section">
                <h2>My Payments</h2>
                <p>See your rent and utility bills and their status.</p>
            </div>
        </div>

        <div class="financial-summary">
            <div class="summary-card outstanding">
                <div class="summary-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="summary-info">
                    <h3>Total Outstanding</h3>
                    <span class="summary-value">₱{{ number_format($summary['total_due'] ?? 0, 2) }}</span>
                </div>
            </div>
            <div class="summary-card collected">
                <div class="summary-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="summary-info">
                    <h3>Total Paid</h3>
                    <span class="summary-value">₱{{ number_format($summary['total_paid'] ?? 0, 2) }}</span>
                </div>
            </div>
            <div class="summary-card pending">
                <div class="summary-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="summary-info">
                    <h3>Upcoming Bills</h3>
                    <span class="summary-value">{{ $summary['upcoming_count'] ?? 0 }}</span>
                </div>
            </div>
            <div class="summary-card revenue">
                <div class="summary-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="summary-info">
                    <h3>Overdue Bills</h3>
                    <span class="summary-value">{{ $summary['overdue_count'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="billing-main">
            <div class="payments-section">
                <div class="section-header">
                    <h3>My Bills</h3>
                </div>
                <div class="payments-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Property / Unit</th>
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
                                <td>
                                    @if($bill->unit)
                                        {{ $bill->unit->property->name ?? 'Property' }} – {{ $bill->unit->unit_number }}
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
                                <td colspan="7" style="text-align:center; padding: 24px;">
                                    You don't have any bills yet. When your landlord starts billing through the system, they will appear here.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="billing-sidebar">
                <div class="quick-actions">
                    <h4>Important</h4>
                    <div class="action-buttons">
                        <p style="font-size: 0.9rem; color: #64748b; margin: 0;">
                            This page is for viewing your bills only. Please continue paying through the usual channels agreed with your landlord.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection



