<!DOCTYPE html>
<html>
<head>
    <title>Tenant Profile Test</title>
</head>
<body>
    <h1>Tenant Profile Test Page</h1>
    <p>If you see this, the route is working!</p>
    <p>User: {{ $tenant->name ?? 'No user' }}</p>
    <p>Email: {{ $tenant->email ?? 'No email' }}</p>
    <p>Assignment: {{ $assignment ? 'Yes' : 'No' }}</p>
    <a href="{{ route('tenant.dashboard') }}">Back to Dashboard</a>
</body>
</html>
