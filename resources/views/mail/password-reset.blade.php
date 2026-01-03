<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="x-apple-disable-message-reformatting" />
  <title>Reset your password</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f8fb;">
  <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">
    Reset your Optima FM password.
  </div>
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#f6f8fb; padding:32px 0;">
    <tr>
      <td align="center" style="padding:0 16px;">
    <!-- Card -->
    <table width="600" cellpadding="0" cellspacing="0" role="presentation"
      style="max-width:600px; background-color:#ffffff; border-radius:16px; overflow:hidden;
             box-shadow:0 10px 30px rgba(16,24,40,0.08);">

      <!-- Header -->
      <tr>
        <td style="padding:32px; background-color:#0b3a3f; text-align:center;">
          <a href="{{ url('/') }}" style="text-decoration: none;">
             <img src="{{ asset('images/logo-white.png') }}"
               alt="Optima FM"
               width="140"
               style="display:block; margin:0 auto 8px auto;" />
          </a>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="padding:36px; font-family:Arial, Helvetica, sans-serif; color:#101828;">

          <h1 style="margin:0 0 12px 0; font-size:22px; line-height:30px; font-weight:700;">
            Reset your password
          </h1>

          <p style="margin:0 0 18px 0; font-size:14px; line-height:22px; color:#344054;">
            Hi {{ $user->name ?? 'there' }},<br><br>
            We received a request to reset the password for your <strong>Optima FM</strong> account.
          </p>

          <!-- CTA -->
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:28px 0;">
            <tr>
              <td align="center">
                <a href="{{ $resetUrl }}"
                   style="background-color:#12b886; color:#ffffff; text-decoration:none;
                          padding:14px 28px; border-radius:12px; font-size:14px;
                          font-weight:700; display:inline-block;">
                  Reset Password
                </a>
              </td>
            </tr>
          </table>

          <p style="margin:0 0 12px 0; font-size:13px; line-height:20px; color:#475467;">
            If the button doesn’t work, copy and paste this link into your browser:
          </p>

          <div style="font-size:12px; line-height:18px; word-break:break-all;
                      background-color:#f2f4f7; color:#0b3a3f;
                      padding:14px; border-radius:10px;">
            {{ $resetUrl }}
          </div>

          <p style="margin:18px 0 0 0; font-size:12px; line-height:18px; color:#667085;">
            This link will expire in 60 minutes.
            If you didn’t request a password reset, you can safely ignore this email.
          </p>

        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="padding:24px 36px; background-color:#f9fafb;
                   font-family:Arial, Helvetica, sans-serif; text-align:center;">
          <p style="margin:0; font-size:12px; line-height:18px; color:#98a2b3;">
            Need help? Reply to this email and we'll help you out.
          </p>
          <p style="margin:6px 0 0 0; font-size:12px; line-height:18px; color:#98a2b3;">
            © {{ date('Y') }} Optima FM. All rights reserved.
          </p>
        </td>
      </tr>

    </table>
  </td>
</tr>
  </table>
</body>
</html>
