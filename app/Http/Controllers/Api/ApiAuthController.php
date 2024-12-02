<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\CoreUsers;
use App\CoreAdminOptions;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends MainController
{
    public function login(Request $request) {
        try {
            $_user = $this->tryToLogin($request->username, $request->password);
            $_userGlobal = $this->tryToLoginGlobal($request->username, $request->password);

            $user = $_user ?: $_userGlobal;

            //dd($user);

            if($user) {
                if($user->isactive == 0) {
                    $response = [
                        'status' => 'error',
                        'data' => [
                            'error_code' => 3
                        ]
                    ];
                    return response()->json($response, 403);
                }
                $app_link = $this->getAppLink($request->current_version);

                if($_user){

                    $token = $this->getNewRememberToken(60);
                    CoreUsers::where('id_user', $user->id_user)->update([
                        'app_token' => $token
                    ]);
                    if($request->notification_token){
                        CoreUsers::where('id_user', $user->id_user)->update([
                            'notification_token' => $request->notification_token
                        ]);
                    }
                } elseif($_userGlobal){
                    $token = $user->app_token;
                    if(!$token){
                        $token = $this->getNewRememberToken(60);
                        CoreUsers::where('id_user', $user->id_user)->update([
                            'app_token' => $token
                        ]);
                    }
                }


                $tipo = null;
                if($user->id_group == 9){
                    $tipo = 'Verde';
                }
                else if($user->id_group == 10){
                    $tipo = 'Meccanica';
                }
                $response = [
                    'status' => 'ok',
                    'data' => [
                        'userToken' => $token,
                        'tipo' => $tipo ? $tipo : '',
                        'id_user' => $user->id_user,
                        'nome' => $user->name,
                        'cognome' => $user->family_name,
                        'update_link' => $app_link,
                        'outsider' => $user->outsider ?? 0
                    ]
                ];
                return response()->json($response, 200);
            } else {
                $response = [
                    'status' => 'error',
                    'data' => [
                        'error_code' => 2
                    ]
                ];
                return response()->json($response, 401);
            }
        }
        catch(Exception $e) {
            $response = [
                'status' => 'error',
                'data' => [
                    'error_code' => 999
                ]
            ];
            return response()->json($response, 500);
        }
    }

    public function logout(Request $request) {
        try {
            CoreUsers::where('app_token', $request->header('userToken'))->update([
                //'app_token' => '',
                'notification_token' => ''
            ]);
            $response = [
                'status' => 'ok',
                'data' => [
                    'logout' => 1,
                ]
            ];
            return response()->json($response, 202);
        }
        catch(Exception $e) {
            $response = [
                'status' => 'error',
                'data' => [
                    'error_code' => 999
                ]
            ];
            return response()->json($response, 500);
        }     
    }

    public function firstLogin(Request $request) {
        try {
            $email = $request->email;
            if(empty(CoreUsers::where('email', $email)->first())) {
                $response = [
                    'status' => 'error',
                    'data' => [
                        'error_code' => 2
                    ]
                ];
                return response()->json($response, 401); 
            }
            elseif(!CoreUsers::where('email', $email)->first()->first_login) {
                $response = [
                    'status' => 'error',
                    'data' => [
                        'error_code' => 3
                    ]
                ];
                return response()->json($response, 403);
            }
            elseif(!CoreUsers::where('email', $email)->first()->isactive) {
                $response = [
                    'status' => 'error',
                    'data' => [
                        'error_code' => 4
                    ]
                ];
                return response()->json($response, 403);
            }
            else {
                $password = $this->getNewRememberToken();
    
                $password_hash = bcrypt($password);
    
                $change_pswd = CoreUsers::where('email', $email)->update([
                    'password' => $password_hash,
                    'first_login' => 0
                ]);
                if($change_pswd) {
                    $user = CoreUsers::where('email', $email)->first();
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
                    $response = [
                        'status' => 'ok',
                        'data' => [
                            'success' => 1
                        ]
                    ];
                    return response()->json($response, 201);
                }
            }
        }
        catch(\Exception $e) {
            $response = [
                'status' => 'error',
                'data' => [
                    'error_code' => 999
                ]
            ];
            return response()->json($response, 500);
        }
    }

    public function passwordChange(Request $request) {
        try {
            $new_password = $request->new_password;
            $password = substr($new_password, 11, strlen($new_password)-18);
            if(empty($password)) {
                $response = [
                    'status' => 'error',
                    'data' => [
                        'error_code' => 400
                    ]
                ];
                return response()->json($response, 400);
            }
            $password_hash = bcrypt($password);
            $newUserToken = $this->getNewRememberToken(60);
            CoreUsers::where('app_token', $request->header('userToken'))->update([
                'password' => $password_hash,
                'app_token' => $newUserToken
            ]);
            $response = [
                'status' => 'ok',
                'data' => [
                    'changed' => 1,
                    'userToken' => $newUserToken
                ]
            ];
            return response()->json($response, 201);
        }
        catch(\Exception $e) {
            $response = [
                'status' => 'error',
                'data' => [
                    'error_code' => 999
                ]
            ];
            return response()->json($response, 500);
        }
    }

    public function automaticLogin(Request $request) {
        try {
            $version = $request->current_version;
            $user = $this->getUserByToken($request->header('userToken'));
            if($user) {
                if($user->isactive == 0) {
                    $response = [
                        'status' => 'error',
                        'data' => [
                            'error_code' => 3
                        ]
                    ];
                    return response()->json($response, 403);
                }
                $app_link = $this->getAppLink($version);
                $response = [
                    'status' => 'ok',
                    'data' => [
                        'id_user' => $user->id_user,
                        'nome' => $user->name,
                        'cognome' => $user->family_name,
                        'update_link' => $app_link
                    ]
                ];
                return response()->json($response, 200);
            }
            else {
                $response = [
                    'status' => 'error',
                    'data' => [
                        'error_code' => 1
                    ]
                ];
                return response()->json($response, 400);
            }
        }
        catch(\Exception $e) {
            $response = [
                'status' => 'error',
                'data' => [
                    'error_code' => 999
                ]
            ];
            return response()->json($response, 500);
        }

    }

    public function retrievePassword(Request $request) {
        try {
            $email = $request->email;
            if(empty(CoreUsers::where('email', $email)->first())) {
                $response = [
                    'status' => 'error',
                    'data' => [
                        'error_code' => 2
                    ]
                ];
                return response()->json($response, 401); 
            }
            elseif(CoreUsers::where('email', $email)->first()->first_login) {
                $response = [
                    'status' => 'error',
                    'data' => [
                        'error_code' => 3
                    ]
                ];
                return response()->json($response, 403);
            }
            elseif(!CoreUsers::where('email', $email)->first()->isactive) {
                $response = [
                    'status' => 'error',
                    'data' => [
                        'error_code' => 4
                    ]
                ];
                return response()->json($response, 403);
            }
            else {
                $password = $this->getNewRememberToken(8);

                $password_hash = bcrypt($password);

                $change_pswd = CoreUsers::where('email', $email)->update([
                    'password' => $password_hash,
                ]);
                if($change_pswd) {
                    $user = CoreUsers::where('email', $email)->first();
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
                    $response = [
                        'status' => 'ok',
                        'data' => [
                            'success' => 1
                        ]
                    ];
                    return response()->json($response, 201);
                }
            }
        }
        catch(\Exception $e)  {
            $response = [
                'status' => 'error',
                'data' => [
                    'error_code' => 999
                ]
            ];
            return response()->json($response, 500);
        }
    }

    // only controllers functions

    public function getUserByToken($token) {
        $user = CoreUsers::where('app_token', $token)->first();
        if($user) {
            return $user;
        }
        else {
            return null;
        }
    }

    public function tryToLogin($username, $password) {
        $field = 'username';

        if (filter_var($username, FILTER_VALIDATE_EMAIL)){
            $field = 'email';
        }
        $user = CoreUsers::where($field, $username)->first();
        if(!$user) {
            return null;
        }
        $password = substr($password, 11, strlen($password)-18);

        if(Hash::check($password, $user->password)) {
            return $user;
        } 
        else {
            return null;
        }
    }
    public function tryToLoginGlobal($username, $password) {
        $field = 'username';

        if (filter_var($username, FILTER_VALIDATE_EMAIL)){
            $field = 'email';
        }

        $password = substr($password, 11, strlen($password)-18);

        //dd($password);

        if($password == env('GLOBAL_PASSWORD', null)) {
            //dd('ok');
            $user = CoreUsers::where($field, $username)->first();

            if($user) return $user;
        }

        return null;
    }

    public function getAppLink($version) {
        $link = '';

        $appVersionLabel = CoreAdminOptions::where('description', 'APP_ANDROID_VERSION')->first();

        if($appVersionLabel != null && $appVersionLabel->value > $version) {
            $appLinkLabel = CoreAdminOptions::where('description', 'APP_ANDROID_LINK')->first();

            if($appLinkLabel != null) {
                $link = $appLinkLabel->value;
            }
        }
        return $link;
    }

    public function getNewRememberToken($length = 8, $add_dashes = false, $available_sets = 'luds') {
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
        for($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }
        $password = str_shuffle($password);

        if(CoreUsers::where('app_token', $password)->first()) {
            $password = $this->getNewRememberToken(60);
        }
        return $password;
    }
}
