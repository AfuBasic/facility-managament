<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Account</title>
    <style>
        body { margin: 0; padding: 0; background-color: #F1F5F9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .header { background: linear-gradient(135deg, #0F172A 0%, #134E4A 100%); padding: 40px 0; text-align: center; }
        .content { padding: 40px; text-align: center; }
        .btn-primary { display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #134E4A 0%, #115E59 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; margin: 24px 0; box-shadow: 0 4px 12px -2px rgba(19, 78, 74, 0.2); transition: transform 0.2s; }
        .text { color: #475569; line-height: 1.6; font-size: 16px; margin-bottom: 24px; }
        .footer { padding: 30px; text-align: center; color: #94A3B8; font-size: 14px; border-top: 1px solid #E2E8F0; }
        .link-text { color: #2DD4BF; word-break: break-all; font-size: 14px; }
    </style>
</head>
<body>
    <div style="padding: 40px 0;">
        <div class="wrapper">
            <div class="header">
                 <h2 style="color: white; margin: 0; font-size: 24px;">Optima FM</h2>
            </div>
            
            <div class="content">
                <h1 style="color: #0F172A; margin-top: 0; font-size: 24px;">Account Reset Information</h1>
                
                <p class="text">Hello {{ $user->name }},</p>
                <p class="text">Your account has been reset by an administrator. Please set a new password to regain access to your account.</p>
                
                <a href="{{ $url }}" class="btn-primary">Set New Password</a>
                
                <p class="text" style="font-size: 14px; color: #64748B;">This link is valid for 1 hour.</p>

                <p class="text" style="margin-top: 32px; font-size: 14px;">If the button doesn't work, copy and paste this link into your browser:</p>
                <a href="{{ $url }}" class="link-text">{{ $url }}</a>
            </div>
            
            <div class="footer">
                &copy; {{ date('Y') }} Optima FM. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
