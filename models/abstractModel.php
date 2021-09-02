<?php

interface IModel
{
    public static function find($id, $whereColumn);
    public static function all();
    public function save();
}


abstract class Model implements IModel
{

    private $remove = ['created_at', 'updated_at', 'password'];
    protected $required = [];
    protected $ignore = array();
    protected $fillable = array();


    public function __construct()
    {
        foreach ($this->ignore as $key => $value) {
            $this->remove[] = $value;
        }

        $this->getRequiredColumns();
        /*DB::getInstance()->query(
            "SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = 'rg'
            AND TABLE_NAME = ".TABLE_PREFIX.get_called_class());
        */
    }

    
    public static function find($id, $whereColumn = "id")
    {
        $query = "SELECT * FROM " . the_object_tablename(get_called_class()) . " WHERE $whereColumn = '$id' LIMIT 1";
        $result = DB::getInstance()->query($query);

        return Model::toObject($result, get_called_class(), true);
    }


    public static function all($foreign_id = null)
    {

        $query = "SELECT * FROM " . the_object_tablename(get_called_class());
        $result = DB::getInstance()->query($query);

        return Model::toObject($result, get_called_class());
    }




    protected function hasMany($object, $foreign_id = null)
    {
        $columnName = strtolower(get_called_class()) . "_id";

        if (!is_null($foreign_id)) {
            $columnName = $foreign_id;
        }

        $result = DB::getInstance()->where($object, $columnName, $this->id);

        $models = [];
        foreach ($result as $key => $value) {
            $model = new $object();
                $model->fromJson( $value );
            $models[] = $model;
        }
        // $this->{object_to_prop_array_name($object)} = $this->toObject($result, $object);
        //$this->{get_called_class() . "s"} = $this->toObject($result, $object);
        // return $this->{object_to_prop_array_name($object)};
        return $this->{strtolower($object."s")  } = $models;
    }


    protected function hasOne( $object, $foreign_id = null ){

        $columnName = strtolower(get_called_class()) . "_id";

        if (!is_null($foreign_id)) {
            $columnName = $foreign_id;
        }

        $result = DB::getInstance()->where($object, $columnName, $this->id);

        $model = new $object();;
        foreach ($result as $key => $value) {
            $model->fromJson( $value );
        }
        // $this->{object_to_prop_array_name($object)} = $this->toObject($result, $object);
        //$this->{get_called_class() . "s"} = $this->toObject($result, $object);
        // return $this->{object_to_prop_array_name($object)};
        return $this->{strtolower($object)  } = $model;
    }


    public function belongsTo( $object ){

        $columnName = strtolower(get_class( $object ) ) . "_id";

        $query = "SELECT * FROM ". the_object_tablename(get_called_class()). " WHERE id=".$this->id ." AND $columnName = ".$object->id;

        if (DB::getInstance()->query($query)){
            return true;
        }

        return false;
    }


    private static function toObject($queryResult, $className, $single = false)
    {

        $objects = array();
        if ($queryResult) {
            while ($row = $queryResult->fetch_assoc()) {
                $object = new $className();
                foreach ($row as $k => $v) {
                    if (!in_array($k, $object->remove)) {
                        $object->{strtolower($k)} = autoCaster($k, $v);
                    }
                }
                $objects[] = (object)$object;
            }

            $queryResult->free_result();

            if ($single) {
                return ( isset( $objects[0] ) )?$objects[0]:false;
            }
        } else {
            return null;
        }

        return $objects;
    }

    public function save2()
    {

        if ($this->checkRequiredFields()) {
            $id = DB::getInstance()->insert($this, get_object_tablename($this));
            return $id;
            // return (new Response($this->find($id)))->sendJson();
        }

        // return (new Response([], false, DB::getInstance()->mysqli->error))->sendJson();
        return false;
    }


    public function save(){
        
        if (!is_array( $errors = $this->checkRequiredFields()) ) {

            $id = DB::getInstance()->insert($this, get_object_tablename($this));
            //return $id;
        

            foreach ($this as $name => $value) {
                if( !in_array($name, ['remove', 'required', 'fillable', 'ignore'])){
                   
                    if( is_array(  $value ) ){
                        foreach ($value as $key => $object) {
                            $class = substr( $name, 0, strlen($name)-1);
                            $model = new $class();
                                $model->{strtolower(get_class($this))."_id"} = $id;
                                $model->fromJson( $object );
                                $model->save();
                        }
                    }else{
                        if( $value instanceof Model ){
                            $value->{get_class($value)."_id"} = $id;
                            $value->save();
                        }
                    }   
                }
                
            }
          
            return (new Response($this->find($id)))->sendJson();
        }else{
            return (new Response($errors, false))->sendJson();
        }
       
    }



    public function update()
    {

        if ($this->checkRequiredFields()) {
            $id = DB::getInstance()->update( $this, get_object_tablename($this) );
            return $this->find($id);
            // return (new Response($this->find($id)))->sendJson();
        }

        //return (new Response([], false, DB::getInstance()->mysqli->error))->sendJson();
    }


    private function checkRequiredFields()
    {

        $errors = [];
        foreach ($this->required as $req) {
            if (!(isset($this->{$req}) && !empty($this->{$req}))) {
                $errors[] =  ['error' => "$req is required"];
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }


    private function getRequiredColumns()
    {
        $query = "select GROUP_CONCAT(column_name) nonnull_columns from information_schema.columns where table_schema = '" . DATABASE_NAME . "' and table_name = '" . get_object_tablename($this) . "' and is_nullable = 'NO'";
        $raw = DB::getInstance()->rowQuery($query, 'nonnull_columns');
        $this->required = explode(',', $raw);
        unset($this->required[0]);
    }


    public function fromJson($jsonString = null)
    {
    
        global $jsonObjectHeader;
        $json = ( $jsonString == null)?$jsonObjectHeader:$jsonString;

        foreach ($json as $attribute => $value) {

            if( !in_array($attribute, ['remove', 'required', 'fillable', 'ignore'])){

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
    }


}
