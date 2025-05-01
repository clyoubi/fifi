<?php 
    
// echo "<script> var ajax_url = ".BASE_URL."</script>";


function stringify($string)
{
    if( preg_match('(id|age)', $string) === 1){
        return (int)$string;
    }
    return sprintf("'%s'", $string);
}

function autoCaster($key, $value){
    if(preg_match('(id|age|period|status)', $key) === 1){
        return (int)$value;
    }

    if( preg_match('(sync|trash)', $key) === 1){
        return boolval($value);
    }

    return sprintf("%s", $value);
}

function get_object_tablename( $object ){
    return strtolower(TABLE_PREFIX . strtolower(get_class($object))."s");
}

function the_object_tablename( $class ){
    return strtolower(TABLE_PREFIX . strtolower( $class )."s");
}

function object_to_prop_array_name( $object ){
    return strtolower($object) . "s";
}


function get_assets($file){
    echo "/assets/".$file;
}

function view($view, $name = '', $args = []) {
    global $datas;

    $datas[$name] = $args;

    // Path to the view file
    $viewPath = "resources/views/$view.php";

    // Extract SEO metadata from the top comments in the view
    $seo = [
        'title' => APP_NAME,
        'description' => APP_DESCRIPTION,
        'image' => APP_ICON
    ];

    if (file_exists($viewPath)) {
        $contents = file_get_contents($viewPath);
        if (preg_match('/\/\*\*([\s\S]*?)\*\//', $contents, $matches)) {
            $metaBlock = trim($matches[1]);

            preg_match_all('/\*\*\s*(\w+):\s*(.+)/', $metaBlock, $metaMatches, PREG_SET_ORDER);
            foreach ($metaMatches as $meta) {
                $key = strtolower(trim($meta[1]));
                $value = trim($meta[2]);
                if (array_key_exists($key, $seo)) {
                    $seo[$key] = $value;
                }
            }
        }
    }

    // Make SEO data globally available
    global $seoMeta;
    $seoMeta = $seo;

    include_once "resources/views/layouts/header.php";
    include_once $viewPath;
    include_once "resources/views/layouts/footer.php";
}



function redirect_to( $url = '' ){
    header("Location: ".BASE_URL."/$url");
    exit();
}


function home_url(){
    echo BASE_URL;
}

function create_nonce(){

}


function get_http_response_code($url) {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}
