<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Login extends REST_Controller {

  /**
   * Index Page for this controller.
   */

  const TABLE = 'login';
  
  public function birsa( ) {
    $this->load->helper( 'cms/model' );
    \cms\model\no_cache( );
    $this->session->unset_userdata( 'cms:rsa:salt' );
    $this->session->unset_userdata( 'cms:login:user:name' );
    $cms_model_login_salt = md5( rand( 0, 9999 ) . time( ) );
    $this->session->set_userdata( 'cms:rsa:salt', $cms_model_login_salt );
    require('./../bhv/biRSA.php');
  }
   
  public function model( $id='undefined' ) {
    $this->load->library( 'birsa' );
    $this->birsa->init( '5abb', '1146bd07f0b74c086df00b37c602a0b', '1d7777c38863aec21ba2d91ee0faf51' );
    // Name of action from Backbone REST API, sorry.
    if ( $this->action === 'create' ) { // register new user
      $this->register_create();
    } else if ( $this->action === 'update' ) { // user is try to login
      $this->login_update( $id );
    }
  }
  
  private function register_create( ) {
      $user = \cms\model\from_json( $this->contents, array( 'name', 'email', 'encryptedpassword' ) );
      $user['password'] = $this->extract_password_md5( $user['encryptedpassword'] );
      $query = $this->db->query( 'select name from cms_user where "name"=?', array( $user['name'] ) );
      if ( $query->num_rows() > 0 ) {
        \cms\model\error_model_header( );
        die( '{error: "User with same name is registered"}' );
      }
      unset ( $user['encryptedpassword'] );
      $this->db->insert( 'cms_user', $user );
      unset ( $user['password'] );
      $user['id'] = $user['name'];
      echo \cms\model\to_json( $user, array( 'name', 'email', 'id' ) );
  }
  
  private function login_update( $id ) {
      $user = \cms\model\from_json( $this->contents, array( 'name', 'encryptedpassword' ) );
      $password = $this->extract_password_md5( $user['encryptedpassword'] );
      $query = $this->db->select('name')->get_where( 'cms_user', array( 'name'=>$user['name'] ) );
      if ( $query->num_rows !== 1 ) {
        \cms\model\error_model_header( );
        die( '{"error":"User not found"}' );
      }
      $query = $this->db->get_where( 'cms_user', array( 'name' => $user['name'], 'password' => $password ) );
      if ( $query->num_rows !== 1 ) {
        \cms\model\error_model_header( );
        die( '{"error":"Not valid password"}' );       
      }  
      $user = $query->row_array( 0 );
      $user['logged'] = parent::get_table_name( );
      $this->session->set_userdata( 'cms:login:user:name', $user['name'] );
      echo \cms\model\to_json( $user, array( 'name', 'email', 'id', 'logged' ) );
  }

  private function extract_password_md5( $salt_password ) {
      $salt_password = $this->birsa->decode( $salt_password );
      $salt = mb_substr( $salt_password, 0, 32 );
      if ( mb_strlen( trim( $salt ) ) !== 32 || $salt !== $this->session->userdata( 'cms:rsa:salt' ) ) {
        \cms\model\error_model_header( );
        die( '{"error":"Do not match salt for encrypted password' );
      }
      $password = trim( mb_substr( $salt_password, 32 ) );
      if ( mb_strlen( $password ) < 4 ) {
        \cms\model\error_model_header( );
        die( '{"error":"Your password is dramatically short"}' );
      }
      return md5( $password );
  }
}