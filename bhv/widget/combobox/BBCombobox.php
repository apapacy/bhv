<?php

class BBCombobox {

  const PDO_CONNECTION_STRING = 'not valid connection string';
  const TABLE = 'no valid table name';

  protected $action;
  protected $contents;

  function __construct( ) {
    $this->action = $this->get_action( );
    $this->contents = $this->get_contents( );
    if ( isset( $_GET['id'] ) ) {
      $this->read( $_GET['id'] );
    } else if ( isset( $_GET['searchValue'] ) ) {
      $this->read_collection( );
    }
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

  protected function _read( $fields, $id, $sid='id' ) {
  // requires: $id IS set ($sid is name key column in real SQL table)
  // output: $model['id'] IS set AND $model[$sid] IS set AND ===
    $select = 'select "' . implode( '","', $fields ) . '" from '
        . $this->get_table_name( ) . ' where "' . $sid . '"=? limit 2';
    $dbh = $this->get_pdo_connection( );
    $sth = $dbh->prepare( $select );
    $sth->execute( array( $id ) );
    $result = $sth->fetch( PDO::FETCH_ASSOC );
    if ( $result === FALSE ) {
      $this->error_model_header( );
      die( '{"error":"SQL - not selected"}' );
    }
    if ( $sth->fetch( PDO::FETCH_ASSOC ) !== FALSE ) {
      $this->error_model_header( );
      die( '{"error":"SQL - more then 1 result"}' );
    }
    if ( $sid !== 'id' ) {
      $result['id'] = $result[$sid];
    }
    echo $this->to_json( $result );
  }

  protected function _read_collection( $fields, $order, $name, $value, $limit, $offset ) {
    $select = 'select "' . implode( '","', $fields ) . '" from '
        . $this->get_table_name( ) 
        . " where \"$name\" like ? order by $order limit $limit offset $offset ";
    $dbh = $this->get_pdo_connection( );
    $sth = $dbh->prepare( $select );
    $value = $value . '%';
    //$sth->bindParam( 1, $value);
    $sth->execute(array($value) );
    $model = array();
    $i = 0;
    while ( $result = $sth->fetch( PDO::FETCH_ASSOC ) ) {
      $model[] = array_merge( $result,  array( 'backbone:combobox:item:id' => $i++ ) );
    }
    for ( $i; $i < $limit; $i++ ) {
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

  private function get_pdo_connection( ) {
    //try {
      $dbh = new PDO( $this->get_pdo_connection_string( ));
      $dbh->query("set client_encoding to UTF8");
      return $dbh;
    //} catch (PDOException $e) {
    //  $this->error_model_header( );
    //  die( '{"error":"SQL - not connected"}' );
    //} 
  }
    
  protected function get_pdo_connection_string( ) {
    return static::PDO_CONNECTION_STRING;
  }

  protected function get_table_name( ) {
    return static::TABLE;
  }
  
}