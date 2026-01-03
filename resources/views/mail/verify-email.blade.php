<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email Address</title>
    <style>
        body { margin: 0; padding: 0; background-color: #F1F5F9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .header { background: linear-gradient(135deg, #0F172A 0%, #134E4A 100%); padding: 40px 0; text-align: center; }
        .content { padding: 40px; text-align: center; }
        .btn { display: inline-block; padding: 14px 30px; background-color: #134E4A; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; margin: 30px 0; transition: background-color 0.3s; }
        .btn:hover { background-color: #0F172A; }
        .text { color: #475569; line-height: 1.6; font-size: 16px; margin-bottom: 24px; }
        .footer { padding: 30px; text-align: center; color: #94A3B8; font-size: 14px; border-top: 1px solid #E2E8F0; }
        .link { color: #2DD4BF; word-break: break-all; }
    </style>
</head>
<body>
    <div style="padding: 40px 0;">
        <div class="wrapper">
            <div class="header">
                 <img src="{{ asset('images/logo-white.png') }}" alt="Optima FM" style="height: 40px; width: auto;">
            </div>
            
            <div class="content">
                <h1 style="color: #0F172A; margin-top: 0; font-size: 24px;">Verify Your Email Address</h1>
                
                <p class="text">Please click the button below to verify your email address. This is required to access all features of your account.</p>
                
                <a href="{{ $url }}" class="btn">Verify Email Address</a>
                
                <p class="text">If you did not create an account, no further action is required.</p>

                <p style="margin-top: 40px; font-size: 12px; color: #94A3B8;">
                    If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser: <br>
                    <a href="{{ $url }}" class="link">{{ $url }}</a>
                </p>
            </div>
            
            <div class="footer">
                &copy; {{ date('Y') }} Optima FM. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
