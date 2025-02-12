<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Export</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid black; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h2>Data Export</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Card No</th>
                <th>Email</th>
                <th>Last Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->card_no }}</td>
                    <td>{{ $row->email }}</td>
                    <td>{{ $row->last_name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
