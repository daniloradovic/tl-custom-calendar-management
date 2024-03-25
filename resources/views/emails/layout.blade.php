<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email</title>
    <style>
        /* Add your custom styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Event Calendar MVP</h1>
        </div>
        <div class="content">
            <div class="container mx-auto bg-gray-200">
                <div class="flex justify-center items-center h-screen">
                    @yield('content')
                </div>
            </div>
        </div>
        <div class="footer">
            <p class="mt-4">Thank you!</p>
        </div>
    </div>
</body>
</html>

