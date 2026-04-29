<!DOCTYPE html>
<html>
<head>
    <title>Donations</title>
</head>
<body>
    <h1>Donation Records</h1>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('donations.create') }}">Add New Donation</a>
    </p>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Hospital</th>
                <th>Location</th>
                <th>Blood Group</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($donations as $donation)
                <tr>
                    <td>{{ $donation->hospital_name }}</td>
                    <td>{{ $donation->hospital_location }}</td>
                    <td>{{ $donation->blood_group }}</td>
                    <td>{{ $donation->donated_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No donations recorded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>