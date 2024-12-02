<?php

namespace App\Http\Controllers\Auth;

use App\CoreAdminOptions;
use App\CoreUsers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class RetrievePasswordController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest');
        $this->data = array();
    }


    public function getReset(Request $request)
    {

        if($request->isMethod('post')){
            return $this->postReset($request);
        }

        //dd($this->generateStrongPassword());

        return view('auth.retrieve_password', $this->data);





    }

    public function postReset($request)
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


        $user = CoreUsers::where('email', $request->email)->first();
 
        if($user){

            if($user->first_login == 1){

                $request->session()->flash('first_ko', 'Non è possibile recuperare la password perché non hai ancora effettuato il primo accesso. Per effettuare il primo accesso all’applicazione usa <a href="'.url("first-login").'">questo link.</a>');

            } else {

                $password = $this->generateStrongPassword();

                $password_hash = bcrypt($password);

                $change_pswd = CoreUsers::where('id_user', $user->id_user)->update([
                    'password' => $password_hash,
                ]);

                if($change_pswd){

                    $adminOptions = CoreAdminOptions::pluck('value', 'description')->toArray();

                    $userData = [
                        'subject' => 'La tua nuova password per accedere a '.$adminOptions['web_application_name'],
                        'to_email_address' => $user->email,
                        'to_name' => $user->name. " " .$user->family_name ,
                        'text' => "
                            <p>Ciao ".$user->name. " " .$user->family_name.",</p>
                            <p>qui sotto puoi trovare la tua nuova password per accedere a ".$adminOptions['web_application_name']." all’indirizzo web</p>
                            <p><a href='".$adminOptions['web_application_url']."'>".$adminOptions['web_application_url']."</a></p>
                            <p>
                                username: ".$user->username."<br>
                                password: ".$password."
                            </p>
                        ",
                    ];

                    //send mail
                    app(\App\Http\Controllers\Auth\EmailController::class)->send($userData, $adminOptions);

                    $request->session()->flash('first_ok_first', 'Username e password sono stati inviati all’indirizzo email inserito.<br>Controlla la tua email (verifica anche la cartella dello spam) e quindi usa le tue username e password per <a href="'.url("login").'">effettuare il login.</a> ');

                } else {
                    $request->session()->flash('first_ko', 'Si è verificato un errore, riprova.');
                }

            }

        } else {

            $request->session()->flash('first_ko', 'L’indrizzo email inserito non è presente nei nostri sistemi; prova a riscriverlo o contatta l’assisstenza.');
        }

        return redirect('retrieve-password');
        
    }


    public function generateStrongPassword($length = 8, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!?#';
        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
        $password = str_shuffle($password);
        if(!$add_dashes)
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }




}
