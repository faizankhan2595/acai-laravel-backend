<?php

use Illuminate\Database\Seeder;
use App\MailTemplate;
class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MailTemplate::create([
            'email_type' => 'welcome_email',
            'subject' => 'Welcome to Project Acai Super Club!',
            'content' => `<p>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Acai</title>


</p>
<div style="width:100%; max-width: 600px; margin:0 auto; padding:50px; border-width:1px; border-color: #6351A2; border-style:dashed; background:#efebf9">
    <div style="padding-bottom: 10px;
        border-bottom: 1px dashed #6351A2;">
        <img src="http://3.1.211.226/img/acai-logo.png" alt="Project-acai" width="100">
    </div>
    <h2 style="color:black; text-align:left;">Dear <span style="font-weight:bold; color:#6351A2;">{{$name}}</span> <br>Your Project Acai membership&nbsp;account is created now.&nbsp;You're all ready to go! </h2>
    <table style="border-collapse: collapse; width: 100%;">
        <tbody>
            <tr style="background:white;">
                <td style="border: 1px dotted #6351A2; text-align: left; padding: 8px; font-weight:bold; color:#424242">Your Email</td>
                <td style="border: 1px dotted #6351A2; text-align: left; padding: 8px; font-weight: bold; color:#6351A2;">{{$email}}</td>
            </tr>
            <tr style="background:white;">
                <td style="border: 1px dotted #6351A2; text-align: left; padding: 8px; font-weight:bold; color:#424242">Your Password</td>
                <td style="border: 1px dotted #6351A2; text-align: left; padding: 8px; font-weight: bold; color:#6351A2;">{{$password}}</td>
            </tr>
        </tbody>
    </table>
    <div style="border-bottom: 1px solid lightgrey">
        <p style="color:grey; margin-bottom: 5px; font-weight: bold;"><span style="color: #212121;">Contact Us</span></p>
        <p style="color:grey; margin-top: 0px; margin-bottom: 5px;"><span style="color: #424242;">Email:</span> hello@project-acai.com</p>

    </div>
    <div style="border-top: 1px solid lightgrey">
        <p style="color:grey;">2020 © Acai. All rights reserved.</p>
    </div>
</div>
<p><br></p>`,
        ]);
        MailTemplate::create([
            'email_type' => 'otp_email',
            'subject' => 'Acai OTP',
            'content' => `<p>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Acai</title>


</p>
<div style="width:100%; max-width: 600px; margin:0 auto; padding:50px; border-width:1px; border-color: #6351A2; border-style:dashed; background:#efebf9">
    <div style="padding-bottom: 10px;
        border-bottom: 1px dashed #6351A2;">
        <img src="http://3.1.211.226/img/acai-logo.png" alt="Project-acai" width="100">
    </div>
    <h2 style="color:black; text-align:left;">Dear <span style="font-weight:bold; color:#6351A2;">{{$name}}</span> <br>Below is your one time passcode:</h2>
    <table style="border-collapse: collapse; width: 100%;">
        <tbody>
            <tr style="background:white;">
                <td style="border: 1px dotted #6351A2; text-align: left; padding: 8px; font-weight: bold; color:#6351A2;">{{ $otp }}</td>
            </tr>
        </tbody>
    </table>
    <div style="border-bottom: 1px solid lightgrey">
        <p style="color:grey; margin-bottom: 5px; font-weight: bold;"><span style="color: #212121;">Contact Us</span></p>
        <p style="color:grey; margin-top: 0px; margin-bottom: 5px;"><span style="color: #424242;">Email:</span> hello@project-acai.com</p>
        <p style="color:grey; margin-top: 0px; margin-bottom: 5px;"><br></p>

    </div>
    <div style="border-top: 1px solid lightgrey">
        <p style="color:grey;">2020 © Acai. All rights reserved.</p>
    </div>
</div>`
        ]);
    }
}
