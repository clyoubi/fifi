<?php

//require_once('../Database/DB.php');

interface Iuser
{
    public function login($params);
    public function logout();
}

class User extends Model implements IUser
{

    /**
     * getSettings
     *
     * @return array(Setting)
     */
    public function getSettings()
    {
        $settings =  $this->hasMany('Setting');
        $args = [];
        foreach ($settings as $key => $setting) {
            $args[$setting->setting_name] = $setting->setting_value;
        }

        $this->settings = $args;
    }

    public function getMedia()
    {
        return $this->hasMany('Media', 'object_id');
    }


    public function login($params)
    {
    }


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
                . '0123456789!@#$%^&*()'); // and any other characters
            shuffle($seed); // probably optional since array_is randomized; this may be redundant
            $rand = '';
            foreach (array_rand($seed, 24) as $k) $rand .= $seed[$k];

            $tokenGeneric = SECRET_KEY . $_SERVER["SERVER_NAME"] . time() . $rand;
            $hash .= hash('sha256', $tokenGeneric);
        }
        if( !User::find($hash, 'token')){
            $this->token = $hash;
        }else{  
            $this->createToken();
        }
    }





}


class Setting extends Model
{
    
}


class Action extends Model{


}