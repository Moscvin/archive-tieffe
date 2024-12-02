<?php

namespace App\Http\Controllers\Auth;

use App\CoreAdminOptions;
use App\CoreUsers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class FirstLoginController extends Controller
{

    public function __construct()
    {
        $this->data = array();
    }

    public function first_login(Request $request){

        if($request->isMethod('post')){
            return $this->check_first_login($request);
        }

        return view('auth.first_login', $this->data);

    }

    public function check_first_login($request)
    {

        $validatedData = Validator::make(Input::all(), [
            'g-recaptcha-response' => 'required|captcha',
            'email' => 'required|string|email|min:3|max:35',
        ]);

        if($validatedData->fails()){

            $request->session()->flash('first_ko', 'Per favore inserisci il tuo indirizzo email.');

            return redirect('first-login')
                ->withErrors($validatedData)
                ->withInput();

        }

        /*$validatedData = $this->validate($request,[
            'email' => 'required|string|email|min:3|max:35',
        ],[
            'email.required' => ' Per favore inserisci il tuo indirizzo email',
        ]);*/


        $user = CoreUsers::where('email', $request->email)->first();
 
        if($user){

            if($user->first_login == 1){

                $password = app(\App\Http\Controllers\Auth\RetrievePasswordController::class)->generateStrongPassword();

                $password_hash = bcrypt($password);
                
                $change_pswd = CoreUsers::where('id_user', $user->id_user)->update([
                    'password' => $password_hash,
                    'first_login' => 0,
                ]);

                if($change_pswd){
                    
                    $adminOptions = CoreAdminOptions::pluck('value', 'description')->toArray();

                    $userData = [
                        'subject' => 'La tua username e password per accedere a '.$adminOptions['web_application_name'],
                        'to_email_address' => $user->email,
                        'to_name' => $user->name. " " .$user->family_name,
                        'text' => "
                            <p>Benvenuto ".$user->name. " " .$user->family_name.",</p>
                            <p>ora puoi accedere a ".$adminOptions['web_application_name']." all’indirizzo web</p>
                            <p><a href='".$adminOptions['web_application_url']."'>".$adminOptions['web_application_url']."</a></p>
                            <p>utilizzando queste credenziali:</p>
                            <p>
                                username: ".$user->username."<br>
                                password: ".$password."
                            </p>
                        ",
                    ];

                    //send mail
                    app(\App\Http\Controllers\Auth\EmailController::class)->send($userData, $adminOptions);

                    $request->session()->flash('first_ok_first', 'Username e password sono stati inviati all’indirizzo email inserito. Controlla la tua email (verifica anche la cartella dello spam) e quindi usa le tue username e password per <a href="'.url("login").'">effettuare il login.</a> ');

                } else {
                    $request->session()->flash('first_ko', 'Si è verificato un errore, riprova.');
                }


            } else {

                $request->session()->flash('first_ok', 'Hai già effettuato il primo login! Se desideri reimpostare la password clicca su questo <a href="'.url("retrieve-password").'">link</a>.');
            }

        } else {

            $request->session()->flash('first_ko', 'L’indrizzo email inserito non è presente nei nostri sistemi; prova a riscriverlo o contatta l’assisstenza.');
        }

        return redirect('first-login');

    }

    public function send_first_login($arr_data)
    {
        $title = $arr_data['title'];
        $content = $arr_data['content'];
        $email = $arr_data['email'];

        \Mail::send('emails.first_login', [
            'title' => $title,
            'content' => $content
        ], function ($message) use ($title, $email)
        {
            $message->from(env("MAIL_FROM_ADDRESS"), $title);

            $message->to($email);

            $message->subject("Primo acceso");

        });

        return true;
    }


    

}
