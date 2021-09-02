<?php

    interface IController{
        public static function create( $params );
        public static function read( $params );
        public static function update( $params );
        public static function delete( $params );
    }


    abstract class Controller implements IController{

        public static function create( $params ){}
        public static function read( $params ){}
        public static function update( $params ){}
        public static function delete( $params ){}

    }