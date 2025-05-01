<?php

    interface IRresponse{
        public static function send(object|array|null $datas, int $statusCode, string $message);
    }
    class Response implements IRresponse{

        public static function send(object|array|null $datas, int $statusCode = 200, string $message=""){
            http_response_code($statusCode);
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    "status"=>$statusCode,
                    "message"=>$message,
                    "datas"=>$datas,
                )
            );
        }
    }