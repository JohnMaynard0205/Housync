<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Review Landlord Documents</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f3f3f3; text-align: left; }
        .status { font-weight: bold; }
        .pending { color: #8a6d3b; }
        .verified { color: #3c763d; }
        .rejected { color: #a94442; }
        form { display: inline-block; }
    </style>
</head>
<body>
    <h1>Documents for {{ $landlord->name }} ({{ $landlord->email }})</h1>
    <p><a href="{{ route('super-admin.pending-landlords') }}">Back to Pending Landlords</a></p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>File</th>
                <th>Uploaded</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($documents as $doc)
                <tr>
                    <td>{{ $doc->id }}</td>
                    <td>{{ str_replace('_',' ', ucfirst($doc->document_type)) }}</td>
                    <td><a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">{{ $doc->file_name }}</a></td>
                    <td>{{ $doc->uploaded_at }}</td>
                    <td class="status {{ $doc->verification_status }}">{{ ucfirst($doc->verification_status) }}</td>
                    <td>
                        <form method="POST" action="{{ route('super-admin.verify-landlord-document', $doc->id) }}">
                            @csrf
                            <input type="hidden" name="status" value="verified">
                            <button type="submit">Verify</button>
                        </form>
                        <form method="POST" action="{{ route('super-admin.verify-landlord-document', $doc->id) }}">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <input type="text" name="notes" placeholder="Reason (optional)" />
                            <button type="submit">Reject</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
<!-- Simple view; integrate into your admin layout if desired. -->
</html>


