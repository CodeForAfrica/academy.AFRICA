<?php
namespace AcademyAfrica\Theme;
/*
Template Name: Login
*/
function get_template(){
    $parts = array('lostpassword'=>'password-reset', "register" => "register", "rp" => "change-password");
    if(isset( $_GET['action'] ) ){
        $action = $_GET['action'];
        if(isset($parts[$action])){
            return 'template-parts/'.$parts[$action];
        }
    }
    return 'template-parts/login';
}

get_header();
$template = get_template();
get_template_part($template, 'template');
get_footer();

?>
