<?php 
if(!defined('ABSPATH')){
    
    exit;
}

/*
plugin description: dynamically replace word in post and content or page

*/

if(!class_exists('Replacer')){
class Replacer{
    public function __construct(){
     add_action("admin_menu", array($this, 'register_menu'),10);
     add_action("replacer_action", array($this ,'replacer'),10);
     
    }
    
    public function register_menu(){
        if(is_admin()){
            add_menu_page('Replacer', 'edit_pages', $array($this,'replacer_settings'), 'search', 30);
        }
    }
    
     public function replacer_settings(){
        if(!is_admin() && current_user_can('manage_options')){
            _e('Denied access, you need admin access','replacer');
        }
    }
    
    public function validator($field){
        if(empty($field)){
             return;
        }
        $field = sanitize_text_field($field);
        $field = trim($field);
        $field =  stripslashes($field);
        $field = htmlspecialchars($field);
        
        return $field;
    }
    
    public function replacer_set(){
        global $wpdb;
        
        if(!isset($_POST['replace_key']) || !isset($_POST['post_types']) || !isset($_POST['search'])){
            $result['status'] = false;
            $msg['message'] = "Empty text";
        }else{
        $total = 0;
        $search = $this->validator($_POST['search'], 'textfield');
        $replace = $this->validator($_POST['replace'], 'textfield');
        
        foreach($_POST['post_types'] as $key => $posttype){
            $posttype = $this->validor($posttype, 'checkbox');
            $res = $wpdb->query(
            $wpdb->prepare(
            "UPDATE" . $wpdb->posts ." SET post_excerpt = REPLACE(post_excerpt, %s, %s),
            post_content = REPLACE(post_content, %s, %s),post_title = REPLACE(post_title, %s, %s) WHERE post_type='$posttype'",
                $search, $replace,$search, $replace,$search, $replace)
            );
            $total =$res + $total;
        }
        $result['message'] = $total + "updated";
        $result['status'] = true;
        
        }
        
        $this->responseJsonResults($result);
    }
    
   private function responseJsonResults($d){
    header('Content-Type: application/json');
    echo json_encode($d);
    wp_die();
   
   }
 
 

}

new Replacer();
}
?>
