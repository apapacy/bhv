<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class MY_Controller extends CI_Controller {}

class REST_Controller extends MY_Controller {

  const TABLE = 'no valid table name';
  
  protected $action;
  protected $contents;

  function __construct( ) {
    parent::__construct();
    $this->load->helper( 'cms/model' );
    $this->action = $this->get_action( );
    $this->contents = $this->get_contents( );
  }

  function test( $msg ) {
    die( $msg );
  }

  protected function create( $fields, $sid=FALSE ) {
  // require:
  // $model['id'] NOT is set (by REST API from Backbone.js)
    $model = $this->from_json( $this->contents, $fields );
    //if ( $sid !== FALSE && ! isset( $model[$id] ) && isset( $model['id'] ) ) {
    // not valid _____________________________________^^^^^^^^^^^^^^^^^^^^^^^^^
    //  $model[$id] = $model['id'];
    //}
    $this->db->insert( TABLE, $model );
    if ( $this->db->affected_rows( ) === 0 ) {
      $this->error_model_header( );
      die( '{"error":"SQL - not inserted"}' );
    }
    if ( $sid !== FALSE && ! isset( $model[$id] ) ) {
      $model['id'] = $model[$sid] = $this->db->insert_id();
    }
    echo $this->to_json( $model );
  }

  protected function read( $fields, $id, $sid='id' ) {
  // requires: $id IS set ($sid is name key column in real SQL table)
  // returns: $model['id'] IS set AND $model[$sid] IS set
    $query = $db->select( $fields )->get_where( TABLE, array( $sid => $id ), 1 /* LIMIT 1 */ );
    if ( $this->db->affected_rows( ) === 0 ) {
      $this->error_model_header( );
      die( '{"error":"SQL - not selected"}' );
    }
    $model = $query->row_array( 0 );
    if ( $sid !== FALSE ) {
      $model['id'] = $model[$sid];
    }
    echo $this->to_json( $model );
  }

  protected function update( $fields, $sid='id' ) {
  // requires: $id IS set ($sid is name key column in real SQL table)
  // $model['id'] IS set (by REST API from Backbone.js)
    $model = $this->from_json( $this->contents, $fields );
    if ( $sid !== 'id' && ! isset( $model[$sid] ) ) {
      $model[$sid] = $model['id'];
      unset( $model['id'] );
    }
    if ( ! isset( $model[$sid] ) ) {
      $this->error_model_header( );
      die( '{"error":"SQL - key value is not set"}' );
    }
    $this->db->update( TABLE, $model, array( $sid => $model[$sid]), 1 /* LIMIT 1 */ );
    if ( $this->db->affected_rows( ) === 0 ) {
      $this->error_model_header( );
      die( '{"error":"SQL - not updated"}' );
    }
    if ( $sid !== FALSE  ) {
      $model['id'] = $model[$sid];
    }
    echo $this->to_json( $model );
  }

  protected function delete( $fields, $id, $sid='id' ) {
  // requires: $id IS set ($sid is name key column in real SQL table)
  // returns: $model['id'] IS set AND $model[$sid] IS set
    $query = $db->delete( TABLE, array( $sid => $id ), 1 /* LIMIT 1 */ );
    if ( $this->db->affected_rows( ) === 0 ) {
      $this->error_model_header( );
      die( '{"error":"SQL - not deleted"}' );
    }
    echo "{ /* record $sid='$id' is deleted */  }";
  }

  
  private function get_action( ) {
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
        return 'delete';
        break;
      case 'PATCH';
        return 'patch';
        break;
      default:
        return 'undefined';
    }
  }

  private function get_contents( ) {
    return file_get_contents('php://input');
  }

  private function no_cache( ) {
    header("Cache-Control: no-store, no-cache,  must-revalidate");
    header("Expires: " .  date("r"));
  }

  private function error_model_header( ) {
    header("HTTP/1.0 409 Conflict");
  }

  private function assoc_fields( $assoc, $filter ) {
   $result = array( );
    foreach ( $assoc as $key => $value ) {
      if ( in_array( $key, $filter ) ) {
        $result[$key] = $value;
      }
    }
    return $result;
  }

  private function from_json( $json, $filter=FALSE ) {
    $assoc = json_decode( $json, TRUE );
    if ( $filter !== FALSE ) {
      return assoc_fields( $assoc, $filter );
    } else {
      return $assoc;
    }
  }

  private function to_json( $assoc, $filter=FALSE ) {
    if ( $filter !== FALSE ) {
      $result = assoc_fields( $assoc, $filter );
      return json_encode( $result );
    } else {
      return json_encode( $assoc );
    }
  }
  
  protected function get_table_name( ) {
    return static::TABLE;
  }

}
