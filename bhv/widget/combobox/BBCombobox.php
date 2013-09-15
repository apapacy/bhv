<?php

class BBCombobox {

  
  
  //protected $action;
  //protected $contents;
  protected $connectionString;
  protected $table;
  protected $keyName;
  protected $searchName;
  protected $displayName;
  protected $order;
  protected $fields;
  
  //protected static $SINGLETON = new static( );

  function __construct( $connectionString, $table, $keyName, $searchName, $displayName, $order, $additionFields=FALSE ) {
    $this->connectionString = $connectionString;
    $this->table = $table;
    $this->keyName = $keyName;
    $this->searchName = $searchName;
    $this->displayName = $displayName;
    $this->order = $order;
    $this->fields = array( $keyName );
    if ( $searchName !== $keyName ) {
      $this->fields[] = $searchName;
    }
    if ( $displayName !== $keyName && $displayName !== $searchName ) {
      $this->fields[] = $displayName;
    }
    //$this->action = $this->get_action( );
    //$this->contents = $this->get_contents( );
    if ( isset( $_GET['id'] ) ) {
      $this->_read( $_GET['id'], $this->keyName );
    } else if ( isset( $_GET['searchValue'] ) ) {
      $this->_read_collection( $this->searchName, $this->order  );
    }
  }

  protected function _read( $id, $sid='id' ) {
  // requires: $id IS set ($sid is name key column in real SQL table)
  // output: $model['id'] IS set AND $model[$sid] IS set AND ===
    $select = 'select "' . implode( '","', $this->fields ) . '" from '
        . $this->table . ' where "' . $sid . '"=? limit 2';
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

  protected function _read_collection( $name, $order ) {
    $value = $_GET['searchValue'];
    $limit =  $_GET['limit'];
    $offset = ( $limit - 1 ) * $_GET['page'];
    $select = 'select "' . implode( '","', $this->fields ) . '" from '
        . $this->table
        . " where \"$name\" like ? order by $order limit $limit offset $offset ";
    $dbh = $this->get_pdo_connection( );
    $sth = $dbh->prepare( $select );
    $value = $value . '%';
    $sth->execute( array( $value ) );
    $model = array();
    $i = 0;
    while ( $result = $sth->fetch( PDO::FETCH_ASSOC ) ) {
      $model[] = array_merge( $result,  array( 'backbone:combobox:item:id' => $i++ ) );
    }
    for ( $i; $i < $limit; $i++ ) {
      $model[] = array_merge( array_fill_keys( $this->fields, 'backbone:combobox:item:undefined' ),
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
  
  /*private function get_action( ) {
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
  }*/

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
      $dbh = new PDO( $this->connectionString );
      $dbh->query("set client_encoding to UTF8");
      return $dbh;
    //} catch (PDOException $e) {
    //  $this->error_model_header( );
    //  die( '{"error":"SQL - not connected"}' );
    //} 
  }
  
}