<?php

interface IDB
{
    //public function select( $query, $className );
    public function where($className, $whereColumn, $columnValue);
    public function insert($object);
    public function update($table, $values);
    public function query($query);
}

class DB implements IDB
{

    private static $instance = null;

    private $mysqli;
    public function __construct()
    {
        $this->mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new DB();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->mysqli;
    }

    public function query($query)
    {

        if ($result =  $this->mysqli->query($query)) {
            return $result;
        }
        return false;
    }

    public function where($object, $whereColumn, $columnValue, $clause = "=")
    {
        $query = "SELECT * FROM " .the_object_tablename( $object ) . " WHERE $whereColumn $clause $columnValue";
        return $this->query($query);
    }

    public function insert($object)
    {

        $keys = array();
        $values = array();


        foreach ($object as $key => $value) {
            if ($key == "password") {
                $value = password_hash( $value, PASSWORD_BCRYPT);
            }

            if( !is_array( $value ) ){
                $keys[] = $key;
                $values[] = $value;
               //unset($object->$key);
            }else{
               
            }
           
        }
        $columnNames = implode(", ", $keys); 

    
        $columnValues = implode(", ", array_map('stringify', $values));
        // $columnValues = implode(", ", $values);
        $query = "INSERT INTO " . get_object_tablename( $object ) . " ( $columnNames ) VALUES (  $columnValues )";

        return ($this->mysqli->query($query))?$this->mysqli->insert_id:$this->mysqli->error;
    }



    /**
     * update
     *
     * @param  string $table
     * @param  array $values
     * @return boolean
     */
    public function update( $values, $table)
    {
       
        $text = [];
        foreach ($values as $key => $value) {
            $text[] = "$key = '$value' ";
        }

        $string = implode(", ", $text);

        $query = "UPDATE $table SET $string WHERE id=$values->id";
        // echo $query;
        //return $string;
       return ($this->mysqli->query($query) === TRUE) ? $values->id : false;
    }


    public function rowQuery($query, $columnNames = null){
        if ($result =  $this->mysqli->query($query)) {
            $rows = $result->fetch_assoc();
        }
        return ( is_null( $columnNames) )?$rows:$rows[$columnNames];
    }

}
