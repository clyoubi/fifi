<?php

class ModelTe
{


    public function fromJson($jsonObject)
    {
        foreach ($jsonObject as $attribute => $value) {
            if (is_array($value)) {

                $json = ltrim((string)json_encode($value));

                // value is an Object
                if (strpos($json, '{') === 0) {
                    $model = new $attribute();
                    $model->fromJson($value);
                    $this->$attribute = $model;
                    //return 'object';
                }

                // value is a list of Objects
                if (strpos($json, '[') === 0) {

                    foreach ($value as $position => $object) {
                        $class = substr( $attribute, 0, strlen($attribute)-1);
                        $model = new $class();
                        $model->fromJson($object);
                        $this->$attribute[] = $model;
                    }
                }
            }else{
                $this->$attribute = $value;
            }
        }
    }

    public function save(){
        
        foreach ($this as $name => $value) {
            if( is_array(  $value ) ){
                echo "\n\n\n";
                foreach ($value as $key => $object) {
                    $class = substr( $name, 0, strlen($name)-1);
                    $model = new $class();
                        $model->fromJson( $object );
                        $model->save();
                }
            }else{
                if( $value instanceof ModelTe ){
                    echo "\n\n\n";
                    $value->save();
                }else{
                    echo "$name : $value";
                    echo "\n";
                }
            }
            
        }
        //return (new Response($this))->sendJson();
    }


}

