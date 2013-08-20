<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Login extends CI_Controller {

  /**
   * Index Page for this controller.
   */

  private $action;
  private $contents;
  private $salt;
   
  public function birsa( ) {
    $this->session->unset_userdata('cms:rsa:salt');
    $this->session->unset_userdata('cms:login:user:name');
    $cms_model_login_salt = $this->session->userdata( 'cms:rsa:salt' );
    //if ( ! $cms_model_login_salt ) {
      $cms_model_login_salt = $this->salt = md5( rand( 0, 9999 ) . time( ) );
      $this->session->set_userdata( 'cms:rsa:salt', $this->salt );
    //}
    require('./../bhv/biRSA.php');
  }
   
  public function model($id = 'undefined') {
    $this->load->helper('cms/model');
    //cms\model\no_cache();
    //$this->load->library('json2');
    $this->load->library('birsa');
    $this->birsa->init('5abb','1146bd07f0b74c086df00b37c602a0b','1d7777c38863aec21ba2d91ee0faf51');
    $this->action = \cms\model\action();
    $this->contents = \cms\model\get_contents();
    if ( $this->action === 'create' ) { // register new user
      $this->register_create();
    } else if ( $this->action === 'update' ) { // user is try to login
      $this->login_update( $id );
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
  
  private function register_create( ) {
      $user = \cms\model\from_json($this->contents, array('name', 'email', 'encryptedpassword'));
      $salt_password = $this->birsa->decode($user['encryptedpassword']);
      $salt = mb_substr( $salt_password, 0, 32 );
      if ( $salt !== $this->session->userdata('cms:rsa:salt') ) {
        \cms\model\error_model_header();
        die('{error: "Do not match salt for encrypted password");
      }
      $query = $this->db->query('select name from cms_user where "name"=?', array( $user['name'] ) );
      if ( $query->num_rows() > 0 ) {
        \cms\model\error_model_header();
        die('{error: "User with same name is registered"}');
      }
      $password = mb_substr( $salt_password, 32 );
      if ( mb_strlen($password) < 4 ) {
        \cms\model\error_model_header();
        die('{error: "Your password is dramatically short"}');
      }
      $user['password']  = md5($password);
      unset ($user['encryptedpassword']);
      $this->db->insert('cms_user', $user);
      unset ($user['password']);
      $user['id'] = $user['name'];
      echo \cms\model\to_json( $user, array('name', 'email', 'id') );
  }
  
  private function login_update( $id ) {
      $user = \cms\model\from_json($this->contents, array('name', 'encryptedpassword'));
      $salt_password = $this->birsa->decode($user['encryptedpassword']);
      $salt = mb_substr( $salt_password, 0, 32 );
      if ( $salt !== $this->session->userdata('cms:rsa:salt') ) {
        \cms\model\error_model_header();
        die( 'Do not match salt for encrypted password' );
      }
      $query = $this->db->query( 'select name from cms_user where name=?', $user['name'] );
      if ( $query->num_rows !== 1 ) {
        \cms\model\error_model_header();
        die( '{"error":"user not found"}' );
      }
      $password = mb_substr( $salt_password, 32 );
      $password = md5($password);
      //$query = $this->db->query( 'select name, password from cms_user where name=? and password=?', array( $user['name'], $password  ));
      $query = $this->db->get_where( 'cms_user', array( 'name' => $user['name'], 'password' => $password ) );
      if ( $query->num_rows !== 1 ) {
        \cms\model\error_model_header();
        die( '{"error":"password mistmach"}' );       
      }
      $user = $query->row_array(0);
      $user['logged'] = TRUE;
      $this->session->set_userdata( 'cms:login:user:name', $user['name'] );
      echo \cms\model\to_json($user, array('name', 'email','id', 'logged'));
  }
  
}

