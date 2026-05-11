<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>System Admin OTP</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f6fa;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 620px;
      margin: 40px auto;
      background: #fff;
      border-radius: 10px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
      overflow: hidden;
    }
    .header {
      background-color: #2c3e50;
      color: #fff;
      text-align: center;
      padding: 20px;
      font-size: 18px;
      font-weight: bold;
      letter-spacing: 0.5px;
    }
    .content {
      padding: 24px;
      color: #333;
      font-size: 15px;
      line-height: 1.6;
    }
    .otp-box {
      text-align: center;
      margin: 22px 0;
    }
    .otp {
      font-size: 30px;
      font-weight: bold;
      color: #2c3e50;
      background-color: #ecf0f1;
      padding: 14px 22px;
      border-radius: 8px;
      letter-spacing: 4px;
      display: inline-block;
    }
    .info {
      font-size: 13px;
      color: #666;
      margin-top: 10px;
    }
    .warning {
      background: #fff7e6;
      border-left: 4px solid #f1c40f;
      padding: 12px 14px;
      border-radius: 6px;
      font-size: 13px;
      color: #7a5b00;
      margin-top: 20px;
    }
    .footer {
      background-color: #f7f7f7;
      text-align: center;
      padding: 16px;
      font-size: 13px;
      color: #888;
    }
    @media (max-width: 480px) {
      .otp { font-size: 24px; padding: 12px 16px; }
      .content { padding: 18px; }
      .header { padding: 16px; font-size: 16px; }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">🔐 System Administrator Login Verification</div>

    <div class="content">
      <p>Hello, <strong>System Administrator</strong>,</p>
      <p>
        An attempt to access the <strong>Admin Page</strong> has been detected.
        To proceed with your login, please use the One-Time Passcode (OTP) below:
      </p>

      <div class="otp-box">
        <span class="otp">{{ $otp }}</span>
      </div>

      <p class="info">
        This OTP will be not valid if the page is <strong>reloaded.</strong> Please do continue to input this otp in order to sign in.
      </p>

      <div class="warning">
        If you did not attempt to log in, please secure the system immediately and reset your admin credentials.
      </div>

      <p style="margin-top: 20px; font-size: 13px; color: #555;">
        This email was automatically sent by your system’s security module.
      </p>
    </div>

    <div class="footer">
      ©2025 — System Security | Admin Verification Service
    </div>
  </div>
</body>
</html>
