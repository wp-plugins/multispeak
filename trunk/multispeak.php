<?php
/*
Plugin Name: Multispeak
Plugin URI: http://www.multispeak.io/
Description: A simple plugin to add Multispeak widget on your pages. This widget will allow to add chat, audio and video support to your pages.
Version: 1.0
License: GPL2
Author: Multispeak
*/

function multispeak_footer(){
  $options = get_option('multispeak_settings');
  echo "<script>
    var _ta=[['_setAccount','{$options['multispeak_company_id']}'],['_videoChat']];
  (function(t,a){var g=t.createElement(a),s=t.getElementsByTagName('body')[0].getElementsByTagName(a)[0];
  g.src='https://widget.multispeak.io/ta.js';g.setAttribute('async',!0);g.setAttribute('charset','utf-8');
  s.parentNode.insertBefore(g,s)}(document,'script'));
      </script>";
  }
  add_action('wp_footer','multispeak_footer');
  //add_action('wp_print_footer_scripts','multispeak_footer');

  if (is_admin()){
    //Admin Menu
    add_action('admin_menu','multispeak_admin_menu');
    add_action('admin_init','multispeak_register_settings');
  }

  function multispeak_register_settings(){
    register_setting('multispeak_settings','multispeak_settings','multispeak_validate_company_id');
    add_settings_section('multispeak_main', '', 'multispeak_section_text', 'multispeak_page_settings');
    add_settings_field('multispeak_company_id', 
      'Multispeak company ID', 
      'multispeak_company_id_string', 
      'multispeak_page_settings', 
      'multispeak_main');
  }

  function multispeak_section_text(){
    echo '<p>You have to login into the <a target="_blank" href="https://multispeak.io/admin">Multispeak admin panel</a> and find the company id number under Settings->Code.</p>';
    echo '<p>If you don\'t have an account with us, you can create one <a target="_blank" href="https://multispeak.io/#get_started">here</a> for free'; 
  }

  function multispeak_company_id_string(){
    $options = get_option('multispeak_settings');
    echo "<input id='multispeak_company_id' name='multispeak_settings[multispeak_company_id]' ".
      "type='text' value='{$options['multispeak_company_id']}' />";
  }

  function multispeak_validate_company_id($input){
    $company_id = $input['multispeak_company_id'];
    if(!(is_numeric($company_id) && strlen(trim($company_id))==10)){
      $input['multispeak_company_id']="";
      $type = 'error';
      $message = __( 'The company id must have 10 digits.', 'my-text-domain' );
    }
    else{
      $type = 'updated';
      $message = __( 'Settings saved.', 'my-text-domain' );
    }
    add_settings_error(
      'myUniqueIdentifyer',
      esc_attr( 'settings_updated' ),
      $message,
      $type
    );
    return $input;
  }


  function multispeak_admin_menu(){
    add_options_page('Multispeak settings','Multispeak','manage_options','multispeak_page_settings','multispeak_admin_content');
  }

  function multispeak_admin_content(){
    if ( !current_user_can( 'manage_options' ) )  {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
?>
  <div class="wrap">  
  <h2> Multispeak widget options</h2>
    <form method="post" action="options.php" id="multispeak_form">
<?php
    settings_fields('multispeak_settings');
    do_settings_sections('multispeak_page_settings');
    submit_button(); 
?>
    </form>
  </div>
<?php
  }

  /* settings link in plugin management screen */
  function multispeak_settings_link($links) {
    $link = '<a href="'. menu_page_url('multispeak_page_settings',false).'">Settings</a>';
		array_unshift($links, $link) ;
    return $links;
  }
  add_filter('plugin_action_links', 'multispeak_settings_link', 2, 2);
?>
