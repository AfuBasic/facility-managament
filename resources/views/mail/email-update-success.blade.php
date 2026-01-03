<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Address Updated</title>
    <style>
        body { margin: 0; padding: 0; background-color: #F1F5F9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .header { background: linear-gradient(135deg, #0F172A 0%, #134E4A 100%); padding: 40px 0; text-align: center; }
        .content { padding: 40px; text-align: center; }
        .text { color: #475569; line-height: 1.6; font-size: 16px; margin-bottom: 24px; }
        .footer { padding: 30px; text-align: center; color: #94A3B8; font-size: 14px; border-top: 1px solid #E2E8F0; }
        .alert { background-color: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 14px; }
    </style>
</head>
<body>
    <div style="padding: 40px 0;">
        <div class="wrapper">
            <div class="header">
                 <img src="{{ asset('images/logo-white.png') }}" alt="Optima FM" style="height: 40px; width: auto;">
            </div>
            
            <div class="content">
                <h1 style="color: #0F172A; margin-top: 0; font-size: 24px;">Security Alert: Email Updated</h1>
                
                <p class="text">Hello {{ $user->name }},</p>
                
                <p class="text">The email address associated with your Optima FM account has been successfully updated to:</p>
                
                <p style="font-weight: bold; font-size: 18px; color: #134E4A; margin: 20px 0;">{{ $newEmail }}</p>
                
                <p class="text">If you made this change, you can safely ignore this email.</p>

                <div class="alert">
                    <strong>Review Security:</strong> If you did NOT authorize this change, please contact our support team immediately as your account security may be compromised.
                </div>
            </div>
            
            <div class="footer">
                &copy; {{ date('Y') }} Optima FM. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
