<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Seller Verification Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            background: #ffffff;
            margin: 30px auto;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }
        h2 {
            color: #2c3e50;
        }
        .details-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 8px 5px;
            border-bottom: 1px solid #f0f0f0;
        }
        .admin-box {
            background: #f9f9f9;
            border-left: 4px solid #2c3e50;
            padding: 15px;
            margin-top: 25px;
            border-radius: 5px;
        }
        .footer {
            margin-top: 25px;
            font-size: 14px;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="email-container">

    <h2>Your Seller Account Has Been Verified! ✔</h2>

    <p>Hi <strong>{{ $data['seller_name'] }}</strong>,</p>

    <p>We are happy to inform you that your seller account has been <strong>successfully verified</strong>.  
       You can now start listing products and enjoy full seller features on our platform.</p>

    <h3>Seller Information</h3>

    <table class="details-table">
        <tr>
            <td><strong>Seller Name:</strong></td>
            <td>{{ $data['seller_name'] }}</td>
        </tr>
        <tr>
            <td><strong>Shop Name:</strong></td>
            <td>{{ $data['shop_name'] }}</td>
        </tr>
        <tr>
            <td><strong>Seller Email:</strong></td>
            <td>{{ $data['seller_email'] }}</td>
        </tr>
        <tr>
            <td><strong>Seller Username:</strong></td>
            <td>{{ $data['seller_username'] }}</td>
        </tr>
    </table>

    {{-- Message from Admin --}}
    @if(!empty($data['message']))
    <div class="admin-box">
        <strong>Message from Admin:</strong>
        <p>{{ $data['message'] }}</p>
    </div>
    @endif

    <p>If you have any questions or need assistance, feel free to contact our support team.</p>

    <p>Thank you,<br>
    <strong>Your Craftify Team</strong></p>

    <div class="footer">
        This is an automated message. Please do not reply.
    </div>

</div>

</body>
</html>
