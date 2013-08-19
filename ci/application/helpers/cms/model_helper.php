<?php namespace cms\model;
if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

function test( ){
  //echo $_SERVER['REQUEST_METHOD'];
  //echo __NAMESPACE__;
}

function action( ) {
  switch ( $_SERVER['REQUEST_METHOD'] ) {
    case 'POST';
      return 'create';
      break;
    case 'GET';
      return 'read';
      break;
    case 'PUT';
      return 'update';
      break;
    case 'DELETE';
      return 'create';
      break;
    case 'PATCH';
      return 'patch';
      break;
    default:
      return 'undefined';
  }
}

function get_contents( ) {
  return file_get_contents('php://input');
}

function no_cache( ) {
  //header("Cache-Control: no-store, no-cache,  must-revalidate");
  //header("Expires: " .  date("r"));
}

function from_json( $json, $filter ) {
  $assoc = json_decode($json, TRUE);
  if ( $filter !== FALSE ) {
    $result = array();
    foreach ( $assoc as $key => $value ) {
      if ( in_array( $key, $filter ) ) {
        $result[$key] = $value;
      }
    }
    return $result;
  }
  return $assoc;
}
