<?php

    interface IRresponse{
        public function sendJson();
    }
    class Response implements IRresponse{

        private $datas = [];
        private $status = false;
        private $message = "";

        public function __construct($datas, $status = true, $message = '')
        {
            $this->datas = $datas;    
            $this->status = $status;    
            $this->message = $message;    
        }


        public function sendJson(){
            echo json_encode(
                array(
                    "status"=>$this->status,
                    "message"=>$this->message,
                    "datas"=>$this->datas,
                )
            );
        }
    }