<?php

class UserController extends Controller
{

    public static function create($params)
    {

        $auth = initUserFromFacebook($params['facebook_id'], $params['facebook_token']);
        //print_r( $auth );
        if ( $auth ) {
      
          if ($user = User::find($auth->facebook_id, 'facebook_id')) {
      
            $user->picture = downloadFacebookPicture($auth->facebook_id);
            //if( $user->phone == ""){ $user->phone = null; }
            //if( $user->age == 0){ $user->age = null; }

            $user->update();
            foreach ($user->getProfiles() as $p) {
              $p->getMetas();
            }
            $user->getSettings();
            //$user->getMedia();
            return (new Response( $user ) )->sendJson();
          
          }else{
      
           // $user = initUserFromFacebook($params['facebook_id'], $params['facebook_token']);
            $auth->createToken();
            $auth->picture = downloadFacebookPicture($auth->facebook_id);
            
            if( $id =$auth->save() ){
              //$auth->getProfiles();
              $u = $auth->find($id);
              $u->getProfiles();
              return (new Response($u))->sendJson();
            }else{
              return (new Response([], false, DB::getInstance()->mysqli->error))->sendJson();
            }
    
          }          
        }
      
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
