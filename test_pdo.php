<?php 

require_once( './bhv/widget/combobox/BBCombobox.php' );


class Test extends BBCombobox {

  /**
   * Index Page for this controller.
   */

  const PDO_CONNECTION_STRING = "pgsql:host=localhost;dbname=Ceh16;user=root;password=26682316";
  const TABLE = 'cennic';
  protected static $FIELDS = array( 'kod', 'name"||\'#\'||"kod" as "det', 'name');

  
  protected function read( $id ) {
    parent::_read( $id, 'kod') ;
  }
  
  protected function read_collection( ) {
    parent::_read_collection( 'name', 'name' );
  }
  
}

$combo = new Test( );
  