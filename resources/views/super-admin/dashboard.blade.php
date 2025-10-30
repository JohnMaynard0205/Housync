@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
  <div class="row mb-4">
    <div class="col-12">
      <h1 class="fw-bold">Super Admin Dashboard</h1>
      <p class="lead">Overview and management of application users and properties.</p>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6 col-xl-3 mb-4">
      <div class="card p-3 text-center">
        <div class="card-body">
          <h5 class="card-title">Total Users</h5>
          <h2 class="fw-bolder">{{ $stats['users'] ?? 0 }}</h2>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3 mb-4">
      <div class="card p-3 text-center">
        <div class="card-body">
          <h5 class="card-title">Total Landlords</h5>
          <h2 class="fw-bolder">{{ $stats['landlords'] ?? 0 }}</h2>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3 mb-4">
      <div class="card p-3 text-center">
        <div class="card-body">
          <h5 class="card-title">Total Properties</h5>
          <h2 class="fw-bolder">{{ $stats['apartments'] ?? 0 }}</h2>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3 mb-4">
      <div class="card p-3 text-center">
        <div class="card-body">
          <h5 class="card-title">Pending Approvals</h5>
          <h2 class="fw-bolder">{{ $stats['pending_landlords'] ?? 0 }}</h2>
        </div>
      </div>
    </div>
  </div>
  <!-- You can put more dashboard widgets, tables, or user/property summaries here -->
</div>
@endsection 