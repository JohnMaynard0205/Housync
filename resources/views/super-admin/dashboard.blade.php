@extends('layouts.super-admin-app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="mb-4">
  <h1 class="fw-bold">Super Admin Dashboard</h1>
  <p class="lead">Overview and management of application users and properties.</p>
</div>
<div class="stats-grid">
  <div class="stat-card"><div class="stat-label">Total Users</div><div class="stat-value">{{ $stats['total_users'] ?? 0 }}</div></div>
  <div class="stat-card"><div class="stat-label">Total Landlords</div><div class="stat-value">{{ $stats['approved_landlords'] ?? 0 }}</div></div>
  <div class="stat-card"><div class="stat-label">Total Properties</div><div class="stat-value">{{ $stats['total_apartments'] ?? 0 }}</div></div>
  <div class="stat-card"><div class="stat-label">Pending Approvals</div><div class="stat-value">{{ $stats['pending_landlords'] ?? 0 }}</div></div>
</div>
@endsection 