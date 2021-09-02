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
     * getProfiles
     *
     * @return array(Profile)
     */
    public function getProfiles()
    {
        return $this->hasMany("Profile");
    }


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

    public static function verifyFacebookToten($facebookToken)
    {

        //$input_token = "EAAKBhhfrd1oBAOGYOw9jdVpZCZAMI8tFXAd0xug2TZBgGueGRalveNTmK7TAfMkWlgHZCaVUt24XnHOs08veUm2bgdj8IWNXCncU9ZCVfJ6ZCBWnNo4unCnLwYd1LzeZB8AMW9H8O1IFpYpKhniulZCZCdnINhZCNCOPyTJ8aS6AzqG9eLMbTtUAZCAeo9mqHEX2szYOj3OWFDMSZAZA3E6OacwoH1D7ZAThnU9eIZD";
        $url = "https://graph.facebook.com/debug_token?input_token=$facebookToken&access_token=".FACEBOOK_ACCESS_TOKEN;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        $json = json_decode($response, true);
        curl_close($ch);

        if ($json['data']['app_id'] == FACEBOOK_APP_ID) {
            $$result = $json['data'];
        }

        $result = false;

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