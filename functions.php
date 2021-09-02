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
    echo BASE_URL."/assets/".$file;
}

function view( $view, $name = '', $args = [] ){
    global $datas;
    $datas[$name] = $args;

    include_once "resources/views/$view.php";
}   


function redirect_to( $url = '' ){
    header("Location: ".BASE_URL."/$url");
    exit();
}


function home_url(){
    echo BASE_URL;
}


function get_http_response_code($url) {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}