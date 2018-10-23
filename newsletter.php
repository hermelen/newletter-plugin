<?php
/*
Plugin Name: newsletter plugin
Description: Plugin pour enregister et visualiser les mails clients

*/

class Zero_Newsletter
{
   public function __construct()
   {
       add_action('wp_loaded', array($this, 'save_email'));
       add_action('admin_menu', array($this, 'add_admin_menu'));
       // add_filter('wp_title', array($this, 'modify_page_title'), 20) ;
   }
   public static function install()
   {
       global $wpdb;

       $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}zero_newsletter_email (id INT AUTO_INCREMENT PRIMARY KEY,
           email VARCHAR(255) NOT NULL);");
   }
   public static function uninstall()
   {
       global $wpdb;

       $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}zero_newsletter_email;");
   }
   public function save_email()
   {
       if (isset($_POST['zero_newsletter_email']) && !empty($_POST['zero_newsletter_email'])) {
           global $wpdb;
           $email = $_POST['zero_newsletter_email'];

           $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}zero_newsletter_email WHERE email = '$email'");
           if (is_null($row)) {
               $wpdb->insert("{$wpdb->prefix}zero_newsletter_email", array('email' => $email));
           }
       }
   }
   public function menu_html()
   {
       echo '<h1>'.get_admin_page_title().'</h1>';
       global $wpdb;

       $results = $wpdb->get_results("SELECT email FROM {$wpdb->prefix}zero_newsletter_email");
       echo '<ul>';
       foreach ($results as $result) {
           ?>
           <li>
               <?php
               echo $result->email;
               ?>
           </li>
         <?php
       }
       echo '</ul>';
   }
   public function add_admin_menu()
   {
       add_menu_page('Liste des mails', 'Newsletter', 'manage_options', 'news', array($this, 'menu_html'));
    }
}

new Zero_Newsletter();



register_activation_hook(__FILE__, array('Zero_Newsletter', 'install'));
// register_deactivation_hook(__FILE__, array('Zero_Newsletter', 'uninstall'));
register_uninstall_hook(__FILE__, array('Zero_Newsletter', 'uninstall'));
