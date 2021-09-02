<?php

interface IAuth
{
    public static function id();
    public function login($user);
    public function validate($user);

    public static function logout();
    public static function user();
}


class Auth implements IAuth
{

    private static $instance =  null;
    private $user = null;

    public function __construct()
    {
        
    }


    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Auth();
        }
        return self::$instance;
    }

    public function login($user)
    {
        //check credetials
        if ($this->validate($user)) {
            $_SESSION['user'] = $user;
            $this->user = $user;
        }
        return (new Response($this->user))->sendJson();
    }




    public function setUser($user)
    {
        $_SESSION['user'] = $user;
        $this->user = $user;
    }

    public static function logout()
    {
        session_destroy();
    }

    public static function user()
    {
        return (isset($_SESSION['user'])) ? $_SESSION['user'] : false;
    }

    public static function id()
    {
        if ($user =  $_SESSION['user']) {
            return (object)$user->id;
        }
    }

    public function validate($user)
    {
        //check user properties
        return true;
    }
}
