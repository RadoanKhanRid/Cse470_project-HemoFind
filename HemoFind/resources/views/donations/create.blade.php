<!DOCTYPE html>
<html>
<head>
    <title>Add Donation</title>
</head>
<body>
    <h1>Add Donation</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('donations.store') }}">
        @csrf

        <div>
            <label>Hospital Name</label>
            <input type="text" name="hospital_name" required>
        </div>

        <div>
            <label>Hospital Location</label>
            <input type="text" name="hospital_location">
        </div>

        <div>
            <label>Blood Group</label>
            <select name="blood_group" required>
                <option value="">Select</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div>
            <label>If Other, enter blood group</label>
            <input type="text" name="other_blood_group" placeholder="Enter blood group here:">
        </div>

        <div>
            <label>Donation Date</label>
            <input type="date" name="donated_at" required>
        </div>

        <button type="submit">Save Donation</button>
    </form>

    <p>
        <a href="{{ route('donations.index') }}">View Donations</a>
    </p>
</body>
</html>