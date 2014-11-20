<?php

/*
 Plugin Name: Leoni BBPress Like Topics
 Plugin URI: http://www.eduardoleoni.com.br
 Description: Let members show their love to the topics they like
 Version: 0.1
 Author: Eduardo Leoni
 Author URI: http://www.eduardoleoni.com.br
 Text Domain: leoni-bbpress-like-topics
 */

if (@$_GET["action"] == "like"){
    
    addToFavorites(get_current_user_id(), $_GET["post"]);
}


function addToFavorites($userID, $postID){
    
    global $wpdb;
    
    $wpdb->insert($wpdb->prefix . "bbpress_likes", array("post_id"=>$postID, "user_id"=>$userID));
   
    
}

function getFavorites($postID){
    
    global $wpdb;
    
    $query = "SELECT * FROM " . $wpdb->prefix . "bbpress_likes WHERE post_id = '$postID'";
    $result = $wpdb->get_results($query);
    
    return count($result);
    
}

function getBar($postID){
   
    ?>
        <span class ="likes_bbpress"> 
            <span class = "counter"><?php echo getFavorites($postID); ?></span>
            <?php if (!get_current_user_id()): ?>
                <span class = "login_to_like"><a href = "#">Login to Like</a></span>
            <?php else: ?>
                <span class = "like"><a href = "?action=like&post=<?php echo $postID; ?>">Like</a></span>
            <?php endif; ?>
        </span>
    <?php
    
}

function shortcodeCaller( $atts ){
    getBar($atts["postid"]);
}

add_shortcode( 'bbpressliketopics', 'shortcodeCaller' );


function leoniBBPressLikeTopicsActivation() {

    global $wpdb;
    
    $query = "CREATE TABLE " . $wpdb->prefix . "bbpress_likes
                (id INT AUTO_INCREMENT,
                post_id INT,
                user_id INT,
                PRIMARY KEY (id))";
    $wpdb->get_results($query);
    
}

register_activation_hook( __FILE__, 'leoniBBPressLikeTopicsActivation' );