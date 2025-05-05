<!DOCTYPE html>
<html>

<body
    style="background-color: #FEFEFE; padding: 20px; font-family: font-size: 14px; line-height: 1.4; font-family: &quot;Helvetica Neue&quot;, &quot;Segoe UI&quot;, Helvetica, Arial, sans-serif;">

    <div style="max-width: 600px; margin: 0px auto; background-color: #fff; box-shadow: 0px 20px 50px rgba(0,0,0,0.05);">
        <table style="width: 100%; background-color: #25293c">
            <tr>
                <td style="background-color: #25293c; text-align: center">
                    <img alt="" src="{{ asset('images/logo.png') }}"
                        style="width: 120px; padding: 15px; vertical-align: middle">
                </td>
            </tr>
        </table>
        <div style="padding: 60px 30px; border-top: 1px solid rgba(0,0,0,0.05);">

            @yield('content')

            <p style="color: #475569; font-size: 14px; margin-top: 32px">
                Best regards,<br>
                {{ config('app.name') }} Team
            </p>

            <h4 style="margin-bottom: 10px; margin-top: 20px; padding-top:20px; border-top: 1px solid rgba(0,0,0,0.05);">
                Need Help?
            </h4>
            <div style="color: #A5A5A5; font-size: 12px;">
                <p><a style="color:#url" href="{{ config('app.client') }}/forgot-password">Forgot your password?</a></p>
                <p>
                    If you have any queries or need assistance, please email us at <a
                        href="mailto:{{ config('mail.support_address') }}"
                        style="text-decoration: underline; color:#1E9FF2;">{{ config('mail.support_address') }}</a>
                    <br>
                    Please don’t reply to this email as it won’t reach us.
                </p>

                @yield('extrafooter')
            </div>


        </div>
        <div style="background-color: #F5F5F5; padding: 40px; text-align: center;">
            <div style="color: #A5A5A5; font-size: 12px; margin-bottom: 20px; padding: 0px 50px;">
                You are receiving this email because you have an account with {{ config('app.name') }} <br>
                To update your newsletter preferences please <a
                    href="{{ config('app.client') }}/account/notifications">click here</a>
            </div>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05);">
                <div style="color: #A5A5A5; font-size: 10px;">
                    Copyright {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
