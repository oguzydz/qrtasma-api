<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


// Models
use App\Models\Users;
use App\Models\Auth;
use App\Models\Animals;
use App\Models\Logs;
use App\Models\Code;


class AnimalController extends Controller
{

    public function checkToken($request) {

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
        } else {
            return array(
                'error' => [
                    'message' => 'The token has not been set!'
                ],
                'code' => 401,
            );
        }
    }

    public function insert(Request $request) {
        $token = $this->checkToken($request);
        $animal_code = $request->route('animal_code');

        $code = $token['code'];

        if ($code !== 401) {
            // burada sorguya başlayabilirsiniz
            // animal inserting
            /*
                inserting data
                ------------
                (itself info)
                name
                surname
                breed
                sex
                birth (dateformat) (it's calculated!)
                -----------
                (display info)
                image - it's about each per month for
                video - will be soon
                gallery
                -----------
                (healthy info)
                aşılar

            */

            $this->validate($request, [
                'animal_name' => 'required',
                'animal_surname' => 'required',
                'animal_breed' => 'required',
                'animal_sex' => 'required',
                'animal_birth' => 'required',
                'animal_image' => 'required',
                'user_id' => 'required'
            ]);


            $bodyContent = $request->getContent();

            $getToken = $request->header('token');
            $userTokenInfo = Auth::where('token', $getToken)->first();


            if ($userTokenInfo['user_id'] === $request->input('user_id')) {

                //burada animal code üretimi gerçekleştirilecek!

                //  -----fikir------
                //  hatta animal code üretimi önceden yapılabilir!
                //  süreci hızlandırmak adına
                // ------bitti------

                $generated_code = $this->animal_code_checking();

                $insertData = [
                    'animal_code' => $generated_code,
                    "animal_name" => $request->input('animal_name'),
                    "animal_surname" => $request->input('animal_surname'),
                    "animal_breed" => $request->input('animal_breed'),
                    "animal_sex" => $request->input('animal_sex'),
                    "animal_birth" => $request->input('animal_birth'),
                    "user_id" => $request->input('user_id'),
                    "animal_image" => $request->input('animal_image')
                ];


                $insert = Animals::insert($insertData);

                if ($insert) {
                    return response()->json([
                        'message' => 'The animal has added!',
                        'code' => 200,
                        'animal_code' => $generated_code
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'The token is not authenticated for current user!',
                    'code' => 401,
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

        $code = $token['code'];

        if ($code !== 401) {
            // burada sorguya başlayabilirsiniz
            // animal inserting
            /*
                inserting data
                ------------
                (itself info)
                name
                surname
                breed
                sex
                birth (dateformat) (it's calculated!)
                -----------
                (display info)
                image - it's about each per month for
                video - will be soon
                gallery
                -----------
                (healthy info)
                aşılar

            */


            $getToken = $request->header('token');
            $userTokenInfo = Auth::where('token', $getToken)->first();


            if ($userTokenInfo['user_id'] === $request->input('user_id')) {

                $animal_code = $request->input('animal_code');
                $animal_id = $request->input('animal_id');

                $data = [
                    'animal_code' => $request->input('animal_code'),
                    "animal_name" => $request->input('animal_name'),
                    "animal_surname" => $request->input('animal_surname'),
                    "animal_breed" => $request->input('animal_breed'),
                    "animal_sex" => $request->input('animal_sex'),
                    "animal_birth" => $request->input('animal_birth'),
                    "user_id" => $request->input('user_id'),
                    "animal_image" => $request->input('animal_image')
                ];

                $getAnimal = Animals::where('animal_code', $animal_code);

                if ($getAnimal) {
                    $update = $getAnimal->update($data);
                    if ($update) {
                        return response()->json([
                            'message' => 'The animal has updated!',
                            'code' => 200,
                            'animal_code' => $animal_code
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Something went wrong!',
                            'code' => 200,
                            'animal_code' => $animal_code
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'The animal code has not registered in the database!',
                        'code' => 401,
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'The token is not authenticated for current user!',
                    'code' => 401,
                ]);
            }
        } else {
            return response()->json([
                'message' => 'The token is not authenticated!',
                'code' => 401,
            ]);
        }
    }

    public function detail(Request $request) {
        $token = $this->checkToken($request);
        $animal_code = $request->route('animal_code');
        $code = $token['code'];

        if ($code !== 401) {
            // burada sorguya başlayabilirsiniz
            // token ına sahip kullanıcının bilgilerini alıp auth kontrolü
            $getToken = $request->header('token');
            $userTokenInfo = Auth::where('token', $getToken)->first();
            $animal = Animals::where('animal_code', $animal_code)->first();

            if ($userTokenInfo['user_id'] === $animal['user_id']) {
                return $animal;
            } else {
                return response()->json([
                    'message' => 'The token is not authenticated for ' . $animal['animal_name'] . '!',
                    'code' => 401,
                ]);
            }
        } else {
            return response()->json([
                'message' => 'The token is not authenticated!',
                'code' => 401,
            ]);
        }
    }


    public function animal_code_checking() {
        $codes = Animals::all('animal_code');
        $code_length = Code::all('code_length')->first()['code_length'];
        $check = 1;

        do {
            foreach ($codes as $code) {
                $generated_code = $this->animal_code_generator($code_length);
                if ($code['animal_code'] !== $generated_code) {
                    $check = 0;
                    return $generated_code;
                } else {
                    return $check++;
                }
            }
        } while ($check !== 0);
    }

    public function animal_code_generator($length) {
        $generated_code = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
        return $generated_code;
    }
}
