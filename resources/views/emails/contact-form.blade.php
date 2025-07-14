<!DOCTYPE html>
<html>

<head>
    <title>New Contact Message From {{ $data['name'] }} - Cabang Dinas Kehutanan Wilayah Trenggalek</title>
</head>

<body>
    <h2>New Contact Message From {{ $data['name'] }} - Cabang Dinas Kehutanan Wilayah Trenggalek</h2>

    <p><strong>Name:</strong> {{ $data['name'] }}</p>
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
    <p><strong>Message:</strong></p>
    <p>{{ $data['message'] }}</p>
</body>

</html>
