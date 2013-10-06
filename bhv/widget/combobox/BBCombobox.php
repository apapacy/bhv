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
  
  protected function validateSettings(array &$settings ) {
    $validKey = array(
      'PDOConnection',
      'connectionString',
      'user',
      'password',
      'encoding',
      'table',
      'keyName',
      'searchName',
      'displayName',
      'order',
      'fields',
    );

    if ( ! isset( $settings['keyName'] ) &&  ! isset( $settings['searchName'] ) && ! isset( $settings['displayName'] ) ) {
      die( 'Table column is not defined.' );
    }

    if (isset( $settings['keyName'] ) &&  ! isset( $settings['searchName'] ) && ! isset( $settings['displayName'] ) ) {
      $settings['searchName'] = $settings['displayName'] = $settings['keyName'];
    }

    if ( ! isset( $settings['keyName'] ) && isset( $settings['searchName'] ) && ! isset( $settings['displayName'] ) ) {
      $settings['displayName'] = $settings['keyName'] = $settings['searchName'];
    }

    if ( ! isset( $settings['keyName'] ) &&  ! isset( $settings['searchName'] ) && isset( $settings['displayName'] ) ) {
      $settings['keyName'] = $settings['searchName'] = $settings['displayName'];
    }
    
    if ( ! isset( $settings['keyName'] )) {
      $settings['keyName'] = $settings['displayName'];
    }
    
    if ( ! isset( $settings['searchName'] )) {
      $settings['searchName'] = $settings['displayName'];
    }
    
    if ( ! isset( $settings['displayName'] )) {
      $settings['displayName'] = $settings['searchName'];
    }
    
    if ( ! isset( $settings['order'] )) {
      $settings['order'] = $settings['searchName'];
    }


    if ( ! isset( $settings['fields'] )) {
      $settings['fields'] = array( );
    }
    
    if ( ! in_array($settings['keyName'], $settings['fields'] ) ) {
      $settings['fields'][] = $settings['keyName'];
    }
    
    if ( ! in_array($settings['searchName'], $settings['fields'] ) ) {
      $settings['fields'][] = $settings['searchName'];
    }

    if ( ! in_array($settings['displayName'], $settings['fields'] ) ) {
      $settings['fields'][] = $settings['displayName'];
    }
   
    foreach ( $settings as $key => $value ) {
      if ( ! in_array( $key, $validKey ) ) {
        die( "'$key' is non valid key" );
      }
    }
  }

  function __construct( $settings ) {
    $this->validateSettings( $settings );
    $this->connection = $this->get_connection( $settings );
    $this->table = $settings['table'];
    $this->keyName = $settings['keyName'];
    $this->searchName = $settings['searchName'];
    $this->displayName = $settings['displayName'];
    $this->order = $settings['order'];
    $this->fields = $settings['fields'];
    //print_r($this->fields);die();
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
    $select = 'select ' . implode( ',', $this->fields ) . ' from '
        . $this->table . ' where ' . $sid . '=? limit 2';
    $dbh = $this->connection;
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
    $select = 'select ' . implode( ',', $this->fields ) . ' from '
        . $this->table
        . " where $name like ? order by $order limit $limit offset $offset ";
    $dbh = $this->connection;
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
  
  private function get_connection( $settings ) {
    if ( isset( $settings['pdoConnection'] ) ) {
      return $setting( 'pdoConnection' );
    }
    try {
      if ( isset( $settings['password'] ) ) {
        $dbh = new PDO( $settings['connectionString'], $settings['user'], $settings['password'] );
      } else if ( isset( $settings['user'] ) ) {
        $dbh = new PDO( $settings['connectionString'], $settings['user'] );
      } else {
        $dbh = new PDO( $settings['connectionString'] );
      }
    } catch (PDOException $e) {
      $this->error_model_header( );
      die( '{"error":"SQL - not connected"}' );
    } 
    if ( isset( $settings['encoding'] ) ) {
      if ( substr( $settings['connectionString'], 0, 2 ) === 'pg' ) {
        $dbh->query("set client_encoding to {$settings['encoding']}");
      } else {
        $dbh->query("set names to '{$settings['encoding']}'");
      }
    }
    return $dbh;
  }
  
}