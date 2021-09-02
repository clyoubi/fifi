<?php


    class Lang{

        public static function get($slug){
            global $lang;
              echo $lang[$slug];
        }


        public static function code(){
            echo (isset($_SESSION['lang']))?$_SESSION['lang']:"fr";
        }

    }