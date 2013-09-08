<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class MY_Controller extends CI_Controller { }

class REST_Controller extends MY_Controller {

  const TABLE = 'no valid table name';

  protected $action;
  protected $contents;

  function __construct( ) {
    parent::__construct();
    $this->action = $this->get_action( );
    $this->contents = $this->get_contents( );
  }

  function test( $msg ) {
    die( $msg );
  }

  public function collection( $id=NULL ) {
    if ( $this->action === 'create' ) {
      $this->test( 'REST create not provided for collection' );
    } else if ( $this->action === 'read' ) {
      $this->read_collection( $id );
    } else if ( $this->action === 'update' ) {
      $this->test( 'REST update not provided for collection' );
    } else if ( $this->action === 'delete' ) {
      $this->test( 'REST delete not provided for collection' );
    }
  }




  public function model( $id=NULL ) {
    if ( $this->action === 'create' ) {
      $this->create( );
    } else if ( $this->action === 'read' ) {
      $this->read( $id );
    } else if ( $this->action === 'update' ) {
      $this->update( $id );
    } else if ( $this->action === 'delete' ) {
      $this->delete( $id );
    }
  }

  protected function _create( $fields, $sid='id' ) {
  // require: $model['id'] NOT set (by REST API from Backbone.js)
    $model = $this->from_json( $this->contents, $fields );
    if ( $sid !== 'id' ) {
      $query = $this->db->get_where( $this->get_table_name( ), array( $sid => $model[$sid] ) );
      if ( $query->num_rows() !== 0 ) {
        $this->error_model_header( );
        die( "{\"error\":\"SQL - dupplicate key $sid='${model[$sid]}'\"}" );
      }
    }
    $this->db->insert( $this->get_table_name( ), $model );
    if ( $this->db->affected_rows( ) === 0 ) {
      $this->error_model_header( );
      die( '{"error":"SQL - not inserted"}' );
    }
    if ( ! isset( $model[$sid] ) ) {
      $model[$sid] = $this->db->insert_id();
    }
    if ( $sid !== 'id' ) {
      $model['id'] = $model[$sid];
    }
    echo $this->to_json( $model );
  }

  protected function _read( $fields, $id, $sid='id' ) {
  // requires: $id IS set ($sid is name key column in real SQL table)
  // output: $model['id'] IS set AND $model[$sid] IS set AND ===
    $query = $this->db->select( $fields )->get_where( $this->get_table_name( ),
             array( $sid => $id ), 1 /* LIMIT 1 */ );
    if ( $query->num_rows() === 0 ) {
      $this->error_model_header( );
      die( '{"error":"SQL - not selected"}' );
    }
    $model = $query->row_array( 0 );
    if ( $sid !== 'id' ) {
      $model['id'] = $model[$sid];
    }
    echo $this->to_json( $model );
  }

  protected function _update( $fields, $id, $sid='id' ) {
  // requires: $id IS set ($sid is name key column in real SQL table)
  // requires: $model['id'] IS set (by REST API from Backbone.js) and $model['id'] === $id
  // effects: to update SQL table and to print JSON object
    $model = $this->from_json( $this->contents, $fields );
    if ( $sid !== 'id'  ) {
      if ( ! isset( $model[$sid] ) ) {
        $model[$sid] = $id;
      }
      unset( $model['id'] );
    }
    if ( ! isset( $model[$sid] ) ) {
      $this->error_model_header( );
      die( '{"error":"SQL - key value is not set"}' );
    }
    $query = $this->db->get_where($this->get_table_name( ), array( $sid => $model[$sid]) );
    if ( $query->num_rows() === 0 ) {
      $this->error_model_header( );
      die( '{"error":"SQL - not selected"}' );
    } else if ( $query->num_rows() > 1 ) {
      $this->error_model_header( );
      die( '{"error":"SQL - don\'t permit multiply update"}' );
    }
    $this->db->update( $this->get_table_name( ), $model, array( $sid => $model[$sid] ) );
    if ( $this->db->affected_rows( ) === 0 ) {
      $this->error_model_header( );
      die( '{"error":"SQL - not updated"}' );
    }
    if ( $sid !== 'id'  ) { // it is possible that value(id) != value(sid)
      $model['id'] = $id;
    }
    echo $this->to_json( $model );
  }

  protected function _delete( $fields, $id, $sid='id' ) {
  // requires: $id IS set ($sid is name key column in real SQL table)
  // effects: delete record $$sid === $id and output @todo
    $query = $this->db->get_where( $this->get_table_name( ), array( $sid => $id ));
    if ( $query->num_rows() === 0 ) {
      $this->error_model_header( );
      die( '{"error":"SQL - not deleted"}' );
    } else if ( $query->num_rows() > 1 ) {
      $this->error_model_header( );
      die( '{"error":"SQL - don\'t permit multiply delete"}' );
    }
    $this->db->delete( $this->get_table_name( ), array( $sid => $id )/*,1 LIMIT 1*/ );
    if ( $this->db->affected_rows( ) === 0 ) {
      $this->error_model_header( );
      die( '{"error":"SQL - not deleted"}' );
    }
    echo "{ /* record $sid='$id' is deleted */  }"; // @todo
  }

  protected function _read_collection( $fields, $order, $name, $value, $limit, $offset ) {
    $query = $this->db->select( $fields, false )->order_by( $order )->
              like( $name, $value, 'after' )->
              get($this->get_table_name( ), $limit, $offset );
    $model = array();
    for ( $i = 0; $i < $query->num_rows( ); $i++ ) {
      $model[] = array_merge( $query->row_array( $i ),  array( 'backbone:combobox:item:id' => $i ) );
    }
    for ( $i = $query->num_rows( ); $i < $limit; $i++ ) {
      $model[] = array_merge( array_fill_keys( $fields, 'backbone:combobox:item:undefined' ),
                                array( 'backbone:combobox:item:id' => $i ) );
    }
    
    echo $this->to_json( $model );
  }

  protected function no_cache( ) {
    header("Cache-Control: no-store, no-cache,  must-revalidate");
    header("Expires: " .  date("r"));
  }

  protected function error_model_header( ) {
    header("HTTP/1.0 409 Conflict");
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
      return $this->assoc_fields( $assoc, $filter );
    } else {
      return $assoc;
    }
  }

  private function to_json( $assoc, $filter=FALSE ) {
    if ( $filter !== FALSE ) {
      $result = $this->assoc_fields( $assoc, $filter );
      return json_encode( $result );
    } else {
      return json_encode( $assoc );
    }
  }

  protected function get_table_name( ) {
    return static::TABLE;
  }

}
