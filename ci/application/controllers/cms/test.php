<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Test extends REST_Controller {

  /**
   * Index Page for this controller.
   */

  const TABLE = 'cms_user';
  
  //public function model( $id='undefined' ) {
  //  if ( $this->action === 'create' ) {
  //    $this->create(array( 'name', 'email', 'password'), 'name');
  //  } else if ( $this->action === 'read' ) {
  //    $this->read(array( 'name', 'email'), $id, 'name');
  //  } else if ( $this->action === 'update' ) {
  //    $this->update(array( 'name', 'email'), $id, 'name');
  //  } else if ( $this->action === 'delete' ) {
  //    $this->update(array( 'name', 'email'), $id, 'name');
  //  }
  //}
  
  protected function create( ) {
    parent::_create(array( 'name', 'email', 'password'), 'name');
  }
  
  protected function read( $id ) {
    parent::_read(array( 'name', 'email', 'password'), $id, 'name');
  }

  protected function update( $id ) {
    parent::_update(array( 'name', 'email', 'password'), $id, 'password');
  }

  protected function delete( $id ) {
    parent::_delete(array( 'name', 'email', 'password'), $id, 'name');
  }
  
}
  