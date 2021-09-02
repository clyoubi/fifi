<?php

interface IRequest
{
    public function get_params();
}


class Router
{
  public $request;
  private $supportedHttpMethods = array(
    "GET",
    "POST",
    "DELETE",
    "PUT"
  );

  private $middleware = [];
  function __construct(IRequest $request)
  {
   $this->request = $request;
   $this->request->jsonObject = json_decode(file_get_contents('php://input'), true);
  }

  function __call($name, $args)
  {

    if( count($args) == 2){
      list($route, $method) = $args;
    }

    if( count( $args ) == 3){
      list($route, $method, $middleware) = $args;
      $this->middleware[$this->formatRoute($route)] = $middleware;
    }
   
    if(!in_array(strtoupper($name), $this->supportedHttpMethods))
    {
      $this->invalidMethodHandler();
    }

    $this->{strtolower($name)}[$this->formatRoute($route)] = $method;

    
  }

  /**
   * Removes trailing forward slashes from the right of the route.
   * @param route (string)
   */
  private function formatRoute($route)
  {
    
    $r = explode('?', $route)[0];
    $result = rtrim($r, '/');
    if ($result === '')
    {
      return '/';
    }
    return $result;

  }

  private function invalidMethodHandler()
  {
    header("{$this->request->serverProtocol} 405 Method Not Allowed");
  }

  private function defaultRequestHandler()
  {
    header("{$this->request->serverProtocol} 404 Not Found");
  }

  /**
   * Resolves a route
   */
  function resolve()
  {
    $methodDictionary = $this->{strtolower($this->request->requestMethod)};
    $formatedRoute = $this->formatRoute( $this->request->requestUri );
    $method = $methodDictionary[$formatedRoute];

    if(is_null($method))
    {
      $this->defaultRequestHandler();
      return;
    }


    if( isset($this->middleware[$formatedRoute] ) ){
      echo call_user_func($this->middleware[$formatedRoute], $method, array($this->request->get_params()) ); 
    }else{
      echo call_user_func_array($method, array($this->request->get_params()));
    }
    
  }


  function __destruct()
  {
    $this->resolve();
  }

  public function get_request(){
    return $this->request;
  }

}



class Request implements IRequest
{

  function __construct()
  {
    $this->bootstrapSelf();
    //print_r($this->serverProtocol);
  }

  private function bootstrapSelf()
  {
    foreach($_SERVER as $key => $value)
    {
      $this->{$this->toCamelCase($key)} = $value;
      //print_r( array($key, $this->toCamelCase($key)) );
    }
  }


  private function toCamelCase($string)
  {
    $result = strtolower($string);
        
    preg_match_all('/_[a-z]/', $result, $matches);

    foreach($matches[0] as $match)
    {
        $c = str_replace('_', '', strtoupper($match));
        $result = str_replace($match, $c, $result);
    }

    return $result;
  }



  public function get_params()
  {

    //print_r( $this );

    if($this->requestMethod === "GET")
      {
        $body = array();
        foreach($_GET as $key => $value)
        {
          $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return $body;
      }

      
      if ($this->requestMethod == "POST")
      {

        $body = array();
        foreach($_POST as $key => $value)
        {
          $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        return $body;
      }


      if ($this->requestMethod == "DELETE")
      {

        $body = array();
        foreach($_GET as $key => $value)
        {
          $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        return $body;
      }


      if ($this->requestMethod == "PUT")
      {

        $body = array();
        foreach($_GET as $key => $value)
        {
          $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        return $body;
      }


  }


}