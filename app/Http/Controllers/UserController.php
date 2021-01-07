<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


// Models
use App\Models\Users;
use App\Models\Auth;
use App\Models\Animals;
use App\Models\Logs;


class UserController extends Controller
{

    public function checkToken($request) {

        $token = $request->header('token');
        $getToken = Auth::where('token', $token)->first();

        $expire_date = $getToken->expire_date;
        $minus = time() - $expire_date;


        if ($getToken) {

            //token süresini hesaplama
            if ($minus >= 3600) {
                return array(
                    'error' => [
                        'message' => 'The token has been expired!'
                    ],
                    'code' => 401,
                );
            } else {
                $insertTokenLogData = [
                    'token' => $token,
                    'headers' => json_encode($request->headers->all()),
                    'body' => $request->getContent(),
                    'url' => $request->fullUrl(),
                    'tarih' => time()
                ];

                $insertTokenLog = Logs::insert($insertTokenLogData);

                if ($insertTokenLog) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function dashboard(Request $request) {
        $token = $this->checkToken($request);
        $user_id = $request->route('user_id');

        $code = $token['code'];

        if ($code !== 401) {
            // burada sorguya başlayabilirsiniz
            $user = Users::where('user_id', $user_id)->first();
            return $user;
        } else {
            return response()->json([
                'message' => 'The token is not authenticated!',
                'code' => 401,
            ]);
        }
    }

    public function detail(Request $request) {
        $token = $this->checkToken($request);
        $user_id = $request->route('user_id');

        $code = $token['code'];

        if ($code !== 401) {
            // burada sorguya başlayabilirsiniz
            $user = Users::where('user_id', $user_id)->first();
            return $user;
        } else {
            return response()->json([
                'message' => 'The token is not authenticated!',
                'code' => 401,
            ]);
        }
    }

    public function animals(Request $request) {
        $token = $this->checkToken($request);
        $user_id = $request->route('user_id');

        $code = $token['code'];

        if ($code !== 401) {
            // burada sorguya başlayabilirsiniz
            $animals = Animals::where('user_id', $user_id)->get();
            return $animals;
        } else {
            return response()->json([
                'message' => 'The token is not authenticated!',
                'code' => 401,
            ]);
        }
    }

    public function password(Request $request) {
        $token = $this->checkToken($request);
        $user_id = $request->route('user_id');

        $code = $token['code'];

        if ($code !== 401) {
            // burada sorguya başlayabilirsiniz
            $password = $request->input('password');
            if ($password) {
                $user = Users::where('user_id', $user_id)->first();
                if ($user) {
                    $encrpytedPass = sha1(md5(sha1(md5($password))));

                    $user->update(['password' => $encrpytedPass]);

                    return response()->json([
                        'message' => 'The password has updated!',
                        'code' => 200
                    ]);
                } else {
                    return response()->json([
                        'message' => 'There is no user which has the id!',
                        'code' => 401
                    ]);
                }

            } else {
                return response()->json([
                    'message' => 'You need to add your password!',
                    'code' => 401
                ]);
            }
        } else {
            return response()->json([
                'message' => 'The token is not authenticated!',
                'code' => 401,
            ]);
        }
    }

    public function edit(Request $request) {
        $token = $this->checkToken($request);
        $user_id = $request->route('user_id');

        $code = $token['code'];

        if ($code !== 401) {
            // burada sorguya başlayabilirsiniz
            $get_user = $request->input('user');
            if ($get_user) {
                $user = Users::where('user_id', $user_id)->first();
                if ($user) {
                    $user->update($get_user);
                    return response()->json([
                        'message' => 'The user has updated!',
                        'code' => 200
                    ]);
                } else {
                    return response()->json([
                        'message' => 'There is no user which has the id!',
                        'code' => 401
                    ]);
                }

            } else {
                return response()->json([
                    'message' => 'You need to add user!',
                    'code' => 401
                ]);
            }
        } else {
            return response()->json([
                'message' => 'The token is not authenticated!',
                'code' => 401,
            ]);
        }
    }


}
