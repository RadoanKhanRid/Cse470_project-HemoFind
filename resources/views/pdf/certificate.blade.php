<div style="text-align: center; border: 5px solid red; padding: 20px; font-family: sans-serif;">
    <h1>HemoFind</h1>
    <hr>
    <h2>Blood Donation Certificate</h2>
    <p>This recognizes that <strong>{{ $donation->donor_name }}</strong></p>
    <p>successfully donated blood type <strong>{{ $donation->blood_type }}</strong>.</p>
    <p>Verified on: {{ \Carbon\Carbon::parse($donation->verified_at)->format('F j, Y - h:i A') }}</p>
</div>