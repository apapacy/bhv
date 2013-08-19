<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

  static $keyPair = 'noinit';


  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   *    http://example.com/index.php/welcome
   *  - or -
   *    http://example.com/index.php/welcome/index
   *  - or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see http://codeigniter.com/user_guide/general/urls.html
   */
  public function model($id = 'undefined') {
    $this->load->helper('cms/model');
    cms\model\no_cache();
    $this->load->library('json2');
    $this->load->library('birsa');
    $this->birsa->init('5abb','1146bd07f0b74c086df00b37c602a0b','1d7777c38863aec21ba2d91ee0faf51');
    $action = \cms\model\action();
    if ( $action === 'create' ) { // register new user
      //$user = json_decode(\cms\model\get_contents(), TRUE);
      //$pwd = $user['encryptedpassword'];
      $user = \cms\model\from_json(\cms\model\get_contents(), array('name', 'encryptedpassword'));
      $user['password'] = $this->birsa->decode($user['encryptedpassword']);
      unset ($user['encryptedpassword']);
      $query = $this->db->query('select name from cms_user where "name"=?', array( $user['name'] ) );
      if ( $query->num_rows() > 0 ) {
        header("HTTP/1.0 409 Conflict");
        echo '{"error":"duplicate login'. $this->birsa->biRandomPadding(7).'"}';    
        return;
      }
      $this->db->insert('cms_user', $user);
      $user = \cms\model\from_json(\cms\model\get_contents(), array('name', 'encryptedpassword'));
      $user['password'] = $this->birsa->decode($user['encryptedpassword']);
      unset ($user['encryptedpassword']);
      echo json_encode($user);
    } else if ( $action = 'update' ) { // user is try to login
      
    }

    //echo cms\model\action();
    //echo cms\model\get_contents();
    //echo $id.'====';
    //$query = $this->db->get('cennic',10,20);
    //foreach ($query->result() as $row) {
     //echo $row->name.'<br />';
    //}
    //echo '{"id":"'. time()  .'"}';
    //echo $this->json2->encode(array('id'=>'русский текст'));
    //echo json_encode(array(/*'id'=>'русский текст'*/));
    //header("HTTP/1.0 409 Conflict");
    //echo '"Что ты себе думаеш"';
  }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */