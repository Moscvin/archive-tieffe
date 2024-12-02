<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\CoreUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $auth;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if($request->password == env('GLOBAL_PASSWORD')){

            $user = CoreUsers::where($field, $request->email)->first();
            if($user){
                Auth::loginUsingId($user->id_user);
                \Cookie::queue('used_global_password', encrypt(true), 99999999);
                return $this->sendLoginResponse($request);
            }
        }
        else if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request) {

        $cookie = \Cookie::get('used_global_password', false);

        $globalLogin = $cookie ? decrypt($cookie) : null;

        if ($globalLogin) {
            //dd('ok');
            $user = Auth::user();
            $rememberToken = $user->remember_token;
            Auth::logout();
            $user->remember_token = $rememberToken;
            $user->save();
            $request->session()->invalidate();
            //$request->session()->regenerateToken();
            \Cookie::queue(\Cookie::forget('used_global_password'));

        } 
        else {
            Auth::logout();
            $request->session()->invalidate();
            //$request->session()->regenerateToken();
        }
        
        return redirect('/');
    }


    public function username()
    {
        $login = request()->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;
    }
}