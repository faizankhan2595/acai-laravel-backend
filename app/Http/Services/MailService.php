<?php
namespace App\Http\Services;

use App\MailTemplate;

class MailService
{
    public function getTemplate($type, $vars)
    {
        $mail = MailTemplate::where('email_type', $type)->first();
        $body = $mail->content;
        if (isset($vars) && count($vars)) {
            foreach ($vars as $key => $val) {
                if ($key == '$url') {$val = "<a href='" . $val . "'>Click Here</a></h1>";}
                $body = str_replace($key, $val, $body);
            }
            $body = str_replace("{{", "", $body);
            $body = str_replace("}}", "", $body);
        }
        return ['subject' => $mail->subject, 'body' => $body];
    }
}
