<?php

//require_once('../Database/DB.php');

interface Iuser
{
    public function login($params);
    public function logout();
}

class User extends Model implements IUser
{

    public int $id;
    public string $lastname;
    /**
        @type:VARCHAR(150)
        @default: John
    **/
    public string $firstname;
    public string $token;

    public function login($params) {}


    public function logout()
    {
        $this->createToken();
        $this->save();
        Auth::getInstance()->setUser($this);
    }


    public function createToken()
    {
        $hash = '';
        for ($i = 0; $i < 3; $i++) {

            $seed = str_split('abcdefghijklmnopqrstuvwxyz'
                . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                . '0123456789!@#$%^&*()');
            shuffle($seed);
            $rand = '';
            foreach (array_rand($seed, 24) as $k) $rand .= $seed[$k];

            $tokenGeneric = SECRET_KEY . $_SERVER["SERVER_NAME"] . time() . $rand;
            $hash .= hash('sha256', $tokenGeneric);
        }
        if (!User::find($hash, 'token')) {
            $this->token = $hash;
        } else {
            $this->createToken();
        }
    }
}