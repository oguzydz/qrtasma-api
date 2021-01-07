<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


// Models
use App\Models\Users;
use App\Models\Auth;
use App\Models\Logs;

class AuthController extends Controller
{

    public function login(Request $request) {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);


        $username = $request->input('username');
        $password = $request->input('password');
        $encrpytedPass = sha1(md5(sha1(md5($password))));


        $user = Users::where('username', $username)->first();
        if ($user) {

            $get_password = Users::where('password', $encrpytedPass)->first();

            if ($get_password) {


                $token = sha1(mt_rand(1, 90000) . 'SALT');
                $tokenInsertData = [
                    'token' => $token,
                    'user_id' => $user['user_id'],
                    'created_date' => time(),
                    'expire_date' => time() + 3600
                ];

                $tokenInsert = Auth::insert($tokenInsertData);

                if ($tokenInsert) {
                    return response()->json([
                        'code' => 200,
                        'user_id' => $user['user_id'],
                        'token' => $token,
                        'expires_in' => 3600
                    ]);
                }
            } else {
                return response()->json([
                    'error' => [
                        'message' => 'The password was wrong!',
                        'code' => 401
                    ]
                ]);
            }

            return response()->json([
                'error' => [
                    'message' => 'The username has been already taken!',
                    'code' => 401
                ]
            ]);
        } else {
            return response()->json([
                'error' => [
                    'message' => 'The username has not been registered!',
                    'code' => 401
                ]
            ]);
        }
    }

    public function logout(Request $request) {

        $token = $request->header('token');
        $getToken = Auth::where('token', $token)->first();

        if ($getToken !== null) {
            //token'ı sil
            $delete = $getToken->delete();
            if ($delete) {
                return array(
                    'error' => [
                        'message' => 'Logged out Successfully!'
                    ],
                    'code' => 200,
                );
            }
        } else {
            return array(
                'error' => [
                    'message' => 'The token has not been set!'
                ],
                'code' => 401,
            );
        }
    }

    public function register(Request $request) {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            'phone' => 'required'
        ]);


        $username = $request->input('username');
        $password = $request->input('password');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $encrpytedPass = sha1(md5(sha1(md5($password))));

        //kullanıcı var mı kontrolü
        $user = Users::find($username);
        if ($user) {
            return response()->json([
                'error' => [
                    'message' => 'The username has been already taken!',
                    'code' => 401
                ]
            ]);
        }

        $email = Users::where('email', $email)->get();
        if (count($email) > 0) {
            return response()->json([
                'error' => [
                    'message' => 'Your email has been already used!',
                    'code' => 401
                ]
            ]);
        }

        $phone = Users::where('phone', $phone)->get();
        if (count($phone) > 0) {
            return response()->json([
                'error' => [
                    'message' => 'Your phone has been already used!',
                    'code' => 401
                ]
            ]);
        }

        $insertData = [
            'real_name' => '',
            'real_surname' => '',
            'identification_number' => '',
            'nationality' => '',
            'user_type' => 'basic',
            'ip_address' => $request->ip(),
            'loggedAt' => time(),
            'createdAt' => time(),
            'username' => $username,
            'password' => $encrpytedPass,
            'email' => $email,
            'phone' => $phone
        ];

        $insert = Users::insert($insertData);

        if ($insert) {
            return response()->json([
                'message' => 'The registiration has made succcessfully',
                'code' => 200,
            ]);
        }
    }

    public function checkToken(Request $request) {

        $token = $request->header('token');
        $getToken = Auth::where('token', $token)->first();

        if ($getToken !== null) {
            $expire_date = $getToken->expire_date;
            $minus = time() - $expire_date;

            //token süresini hesaplama
            if ($minus >= 3600) {
                return array(
                    'error' => [
                        'message' => 'The token has been expired!'
                    ],
                    'code' => 401,
                );
            } else {

                //admin controller
                //checking user type
                $user_id = $getToken->user_id;
                $user = Users::where('user_id', $user_id)->first();


                if ($user->user_type === "admin") {
                    $insertTokenLogData = [
                        'token' => $token,
                        'headers' => json_encode($request->headers->all()),
                        'body' => $request->getContent(),
                        'url' => $request->fullUrl(),
                        'tarih' => time()
                    ];
                    $insertTokenLog = Logs::insert($insertTokenLogData);
                    return true;
                } else {
                    $insertTokenLogData = [
                        'token' => $token,
                        'headers' => json_encode($request->headers->all()),
                        'body' => $request->getContent(),
                        'url' => $request->fullUrl(),
                        'tarih' => time()
                    ];
                    $insertTokenLog = Logs::insert($insertTokenLogData);
                    return false;
                }
            }
        } else return array(
            'error' => [
                'message' => 'The token has not been set!'
            ],
            'code' => 401,
        );
    }

    public function checkInput(Request $request) {
        $token = $request->header('token');
        $getToken = Auth::where('token', $token)->first();

        if ($getToken !== null) {

            $inputField = $request->input('inputField');
            $inputValue = $request->input('inputValue');

            $getInput = Users::where([$inputField => $inputValue])->first();

            if ($getInput) {
                return array(
                    'message' => 'There is the value',
                    'code' => 401,
                );
            }else {
                return array(
                    'message' => 'There is no the value!',
                    'code' => 200,
                );
            }

        } else return array(
            'error' => [
                'message' => 'The token has not been set!'
            ],
            'code' => 401,
        );
    }
}
