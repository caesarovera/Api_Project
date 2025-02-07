<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\User;

class UserController extends Controller
{
    private $sucess_status = 200;
    
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'first_name' => 'required',
                'last_name'  => 'required',
                'email'      => 'required|email',
                'password'   => 'required|alpha_num|min:12'
            ]
        );

        if($validator->fails()) {
            return response()->json(["validation_errors" => $validator->errors()]);
        }

        $dataArray = array(
            "first_name" => $request->first_name,
            "last_name"  => $request->last_name,
            "email"      => $request->email,
            "password"   => bcrypt($request->password),

        );

        $user = User::create($dataArray);

        if(!is_null($user)) {
            return response()->json(["status" => $this->sucess_status, "success" => true, "data" => $user]);
        }

        else {
            return response()->json(["status" => "failed", "success" => false, "message" => "Whoops! user not created. please try again."]);
        }
    }

    public function userLogin(Request $request) {

        $validator = Validator::make($request->all(),
            [
                'email'    => 'required|email',
                'password' => 'required|alpha_num|min:12'
            ]
        );

        if($validator->fails()) {
            return response()->json(["validation_errors" => $validator->errors()]);
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user  = Auth::user();
            $token = $user->createToken('token')->accessToken;

            return response()->json(["status" => $this->sucess_status, "success" => true, "login" => true, "token" => $token, "data" => $user]);
        }
        else {
            return response()->json(["status" => "failed", "success" => false, "message" => "Whoops! invalid email or password"]);
        }
    }
    
    public function userDetail() {
        $user = Auth::user();
        if(!is_null($user)) {
            return response()->json(["status" => $this->sucess_status, "success" => true, "user" => $user]);
        }
        else {
            return response()->json(["status" => "failed", "success" => false, "message" => "Whoops! no user found"]);
        }
    }
}
