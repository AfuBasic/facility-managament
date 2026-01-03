<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Code</title>
    <style>
        body { margin: 0; padding: 0; background-color: #F1F5F9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .header { background: linear-gradient(135deg, #0F172A 0%, #134E4A 100%); padding: 40px 0; text-align: center; }
        .content { padding: 40px; text-align: center; }
        .otp-code { font-size: 36px; font-weight: bold; letter-spacing: 4px; color: #134E4A; margin: 30px 0; padding: 20px; background-color: #F0FDFA; border-radius: 8px; border: 2px dashed #2DD4BF; display: inline-block; }
        .text { color: #475569; line-height: 1.6; font-size: 16px; margin-bottom: 24px; }
        .footer { padding: 30px; text-align: center; color: #94A3B8; font-size: 14px; border-top: 1px solid #E2E8F0; }
    </style>
</head>
<body>
    <div style="padding: 40px 0;">
        <div class="wrapper">
            <div class="header">
                 <img src="{{ asset('images/logo-white.png') }}" alt="Optima FM" style="height: 40px; width: auto;">
            </div>
            
            <div class="content">
                <h1 style="color: #0F172A; margin-top: 0; font-size: 24px;">Verify Your Email</h1>
                
                <p class="text">You have requested to update your email address. Please use the verification code below to confirm this change.</p>
                
                <div class="otp-code">{{ $otp }}</div>
                
                <p class="text">This code will expire in 10 minutes. If you did not request this change, please ignore this email.</p>
            </div>
            
            <div class="footer">
                &copy; {{ date('Y') }} Optima FM. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
