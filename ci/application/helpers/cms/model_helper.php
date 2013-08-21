<?php namespace cms\model;
if (!defined('BASEPATH')) exit('No direct script access allowed');

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
  header("Cache-Control: no-store, no-cache,  must-revalidate");
  header("Expires: " .  date("r"));
}

function error_model_header( ) {
  header("HTTP/1.0 409 Conflict");
}

function assoc_fields( $assoc, $filter ) {
 $result = array();
  foreach ( $assoc as $key => $value ) {
    if ( in_array( $key, $filter ) ) {
      $result[$key] = $value;
    }
  }
  return $result;
}

function from_json( $json, $filter=FALSE ) {
  $assoc = json_decode($json, TRUE);
  if ( $filter !== FALSE ) {
    return assoc_fields($assoc, $filter);
  } else {
    return $assoc;
  }
}

function to_json( $assoc, $filter=FALSE ) {
  if ( $filter !== FALSE ) {
    $result = assoc_fields($assoc, $filter);
    return json_encode($result);
  } else {
    return json_encode($assoc);
  }
}

