<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Test extends REST_Controller {

  /**
   * Index Page for this controller.
   */

  const TABLE = 'cms_user';
  
  public function model( $id='undefined' ) {
    if ( $this->action === 'create' ) {
      $this->create(array( 'name', 'email', 'password'), 'name');
    } else if ( $this->action === 'read' ) {
      $this->read(array( 'name', 'email'), $id, 'name');
    } else if ( $this->action === 'update' ) {
      $this->update(array( 'id', 'name', 'email'), $id, 'name');
    } else if ( $this->action === 'delete' ) {
      $this->update(array( 'name', 'email'), $id, 'name');
    }
  }
 
}
  