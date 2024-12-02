<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\CoreAdminOptions;


class EmailController extends Controller
{

    public function __construct()
    {
        $this->data = array();
    }


    public function send($userData, $adminOptions = [])
    {
        $adminOptions = CoreAdminOptions::pluck('value', 'description')->toArray();
        \Config::set('mail.driver', $this->getoption('MAIL_DRIVER'));
        \Config::set('mail.host', $this->getoption('MAIL_HOST'));
        \Config::set('mail.port', $this->getoption('MAIL_PORT'));
        \Config::set('mail.username', $this->getoption('MAIL_USERNAME'));
        \Config::set('mail.password', $this->getoption('MAIL_PASSWORD'));
        \Config::set('mail.encryption', $this->getoption('MAIL_ENCRYPTION'));
        \Config::set('mail.from.address', $this->getoption('email_address_from'));
        \Config::set('mail.from.name', $this->getoption('email_name_from'));


        \Mail::send('emails.general_email', [

            'text' => $userData['text'],

            'adminOptions' => $adminOptions

        ], function ($message) use ($userData, $adminOptions)
        {
            $message->from($adminOptions['email_address_from'], $adminOptions['email_name_from']);
            $message->to($userData['to_email_address']);
            if(!empty($adminOptions['email_ccn_address'])){
                $message->bcc($adminOptions['email_ccn_address']);
            }

            if(($userData['cc_email_address'] ?? false)&& count($userData['cc_email_address'])){
                foreach($userData['cc_email_address'] as $mail) {
                    $message->cc($mail);
                }
            }

            if($userData['attachment'] ?? false){
                $message->attachData($userData['attachment'], $userData['attachmentName']);
            }

            $message->subject($userData['subject']);

        });

        return true;
    }


    public function send2($userData, $adminOptions = [])
    {
        $adminOptions = CoreAdminOptions::pluck('value', 'description')->toArray();
        \Config::set('mail.driver', $this->getoption('MAIL_DRIVER'));
        \Config::set('mail.host', $this->getoption('MAIL_HOST'));
        \Config::set('mail.port', $this->getoption('MAIL_PORT'));
        \Config::set('mail.username', $this->getoption('MAIL_USERNAME'));
        \Config::set('mail.password', $this->getoption('MAIL_PASSWORD'));
        \Config::set('mail.encryption', $this->getoption('MAIL_ENCRYPTION'));
        \Config::set('mail.from.address', $this->getoption('email_address_from'));
        \Config::set('mail.from.name', $this->getoption('email_name_from'));


        \Mail::send('emails.custom_email1', [

            'text' => $userData['text'],

            'adminOptions' => $adminOptions

        ], function ($message) use ($userData, $adminOptions)
        {
            $message->from($adminOptions['email_address_from'], $adminOptions['email_name_from']);
            $message->to($userData['to_email_address']);
            if(!empty($adminOptions['email_ccn_address'])){
                $message->bcc($adminOptions['email_ccn_address']);
            }

            if(($userData['cc_email_address'] ?? false)&& count($userData['cc_email_address'])){
                foreach($userData['cc_email_address'] as $mail) {
                    $message->cc($mail);
                }
            }

            if($userData['attachment'] ?? false){
                $message->attachData($userData['attachment'], $userData['attachmentName']);
            }

            $message->subject($userData['subject']);

        });

        return true;
    }

    public static function getoption($key){
        $option = CoreAdminOptions::where('description',$key)->first();
        return $option->value;
    }

    

}
