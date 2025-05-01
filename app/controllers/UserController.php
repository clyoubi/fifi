<?php

class UserController extends Controller
{

    public static function create($params)
    {
      
    }

    public static function read($params)
    {
        $user = $params['user'];
     
            foreach ($user->getProfiles() as $p) {
                $p->getMetas();
                //$p->getActions();
            }
            $user->getSettings();
            $user->getMedia();
     
        return (new Response($user))->sendJson();
    }

    public static function update($params)
    {
      $user = $params['user'];

      
      $setting = new Setting();
      $setting->user_id = $user->id;
      $setting->setting_name = $params['setting_name'];
      $setting->setting_value = $params['setting_value'];
      
      $id = DB::getInstance()->rowQuery("SELECT * FROM ".TABLE_PREFIX."settings WHERE setting_name='$setting->setting_name' AND user_id=$setting->user_id", "id");
      
      if( $id ){
         $setting->id = $id;
         $setting->update();
      }else{
        $id = $setting->save();
      }

        return (new Response([$id], $id))->sendJson();
    }


    public static function delete( $params )
    {

        $user = $params['user'];

        $return = DB::getInstance()->query("DELETE FROM ".the_object_tablename("User")." WHERE id=$user->id AND token=$user->token");

        return (new Response([], $return))->sendJson();
    }


}
