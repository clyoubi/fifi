<?php

class MediaController extends Controller
{

    public static function upload($params, $files)
    {

        $user = $params['user'];

            $profile = User::find($params['id']);

            if ($profile->belongsTo($user)) {

                $path =  "/public/uploads/profiles/" . date('Y') . "/" . date('m') . "/";
                $dir = BASE_PATH .$path;

                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                if (isset($files['image']['name'])) {

                    $file_name = time() . basename($files['image']['name']);

                    $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') {

                    $end_name = $profile->id. "_" .$profile->phone . "_" . $user->id . "_" . time() . "." .$extension;

                        if ($files["image"]["size"] < 4000001) {
                            $file = $dir . $end_name;
                            if (move_uploaded_file($files['image']['tmp_name'], $file)) {

                                $profile->picture = BASE_URL . $path . $end_name;
                                    $profile->update();

                                    $media = new Media();
                                    $media->object_id = $profile->id;
                                    $media->name = $profile->name;
                                    $media->phone = $profile->phone;
                                    $media->type = get_class( $profile );
                                    $media->url = $profile->picture;
                                    $id = $media->save();


                                $arr = array(
                                    'status' => 1,
                                    'message' => "File Uploaded",
                                    'file_name' => $end_name,
                                    'url' => $profile->picture,
                                );
                                
                            } else {
                                $arr = array(
                                    'status' => 0,
                                    'error' => "Something Went Wrong Please Retry",
                                    'file_name' => $end_name
                                );
                            }
                        } else {
                            $arr = array(
                                'status' => 0,
                                'error' => "File size cant exceed 4 MB"
                            );
                        }
                    } else {
                        $arr = array(
                            'status' => 0,
                            'error' => "Only .png, .jpg and .jpeg format are accepted"
                        );
                    }
                } else {
                    $arr = array(
                        'status' => 1,
                        'message' => "Please try Post Method"
                    );
                }

                return (new Response($arr, ($arr['status'] == 1) ? true : false))->sendJson();
            }else{
                return (new Response(null, false, "This profile does not belongs to you!"))->sendJson();
 
            }
        

        return false;
    }

    public static function read($params)
    {
        $file = $params['file'];
        $content_type = $params['content_type'];
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header("Content-Type: $content_type");
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
        } else {
            http_response_code(404);
            echo "File not found.";
        }
    }

    public static function update($params)
    {
    }

    public static function delete($params)
    {
    }

    public static function download($params)
    {
        $file = $params['file'];
        $content_type = $params['content_type'];
        $content_disposition = $params['content_disposition'];
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header("Content-Type: $content_type");
            header("Content-Disposition: $content_disposition");
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
        } else {
            http_response_code(404);
            echo "File not found.";
        }
    }
}
