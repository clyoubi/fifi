<?php

    class AuthMiddleware{
        
        public static function index($method, $params){
            if (array_key_exists('token', $params[0])) {
                $user = User::find($params[0]['token'], 'Authorization');

                if (!is_null($user) && !empty($user)) {
                    $params[0]["user"] = $user;
                    echo call_user_func_array($method, $params);
                }else{
                    return Response::send([], 400, "The user can't be authenficated" );
                }
            }else {
                return Response::send([], 400, "The user can't be authenficated" );
            }

        }
    }