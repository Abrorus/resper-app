<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Inventory - RESPER</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f9; color: #333; padding: 20px; }
        .container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        h1 { font-size: 24px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; color: #555; }
        .action-btn { background-color: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Restaurant Inventory</h1>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>ID</th>
                    <th>Total Inventory</th>
                    <th>Last Update</th>
                    <th>Manage By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Lorem ipsum</td>
                    <td>502623313</td>
                    <td>512</td>
                    <td>03/01/2026</td>
                    <td>Lorem Ipsum</td>
                    <td><button class="action-btn">Delete</button></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Lorem ipsum</td>
                    <td>502623313</td>
                    <td>123</td>
                    <td>03/01/2026</td>
                    <td>Lorem Ipsum</td>
                    <td><button class="action-btn">Delete</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>