<?php

    class AuthMiddleware{
        
        public static function index($method, $params){
            
            $user = User::find($params[0]['token'], 'token');

            if (!is_null($user) && !empty($user)) { 
                $params[0]["user"] = $user;
                echo call_user_func_array($method, $params);
            }else{
                return (new Response([], false, "The user can't be authenficated" ) )->sendJson();
            }
    
        }

        
    }