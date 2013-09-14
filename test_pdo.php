<?php 

require_once( './bhv/widget/combobox/BBCombobox.php' );


class Test extends BBCombobox {

  /**
   * Index Page for this controller.
   */

  const PDO_CONNECTION_STRING = "pgsql:host=localhost;dbname=Ceh16;user=root;password=26682316";
  const TABLE = 'cennic';
  

  
  protected function read( $id ) {
    parent::_read(array( 'kod', 'det', 'name'), $id, 'kod');
  }
  
  protected function read_collection( ) {
    parent::_read_collection( 
                  array( 'kod', 'name"||\'#\'||"kod" as "det', 'name'),
                  'name',
                  'name',
                  $_GET['searchValue'],
                  $_GET['limit'], ($_GET['limit']-1)*$_GET['page']
    );
  }
  
}

$combo = new Test( );
  