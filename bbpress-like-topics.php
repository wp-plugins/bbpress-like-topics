<?php
/*
 Plugin Name: bbPress Like Topics
 Plugin URI: http://www.eduardoleoni.com.br
 Description: Let members show their love to the topics they like
 Version: 1.2
 Author: Eduardo Leoni
 Author URI: http://www.eduardoleoni.com.br
 Text Domain: bbpress-like-topics
 */


/* OLD
if (@$_GET["action"] == "like"){
    
    addToFavorites(get_current_user_id(), $_GET["post"]);
}
*/

function addToFavorites($userID, $postID){
    
    global $wpdb;
    
    $wpdb->insert($wpdb->prefix . "bbpress_likes", array("post_id"=>$postID, "user_id"=>$userID));
  
}

function removeFromFavorites($userID, $postID){
    
    global $wpdb;
    
    $wpdb->get_results("DELETE FROM " . $wpdb->prefix . "bbpress_likes WHERE post_id = '$postID' AND user_id = '$userID'");
  
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
            <span class = "counter_<?php echo $postID; ?>"><?php echo getFavorites($postID); ?></span>
        </span>
    <?php
    
}

function getBar_withLike($postID){
   
    ?>
        <span class ="likes_bbpress"> 
            <span class = "counter_<?php echo $postID; ?>" id = "<?php echo $postID; ?>"><?php echo getFavorites($postID); ?></span>
            <?php if (!get_current_user_id()): ?>
                
            <?php else: ?>
                <?php if (checkIfHasAlreadyLiked($postID)): ?>
                    <span class = "like like_<?php echo $postID; ?>"><a href = "javascript: unlikeIt(<?php echo $postID; ?>);">Unlike</span>
                <?php else: ?>
                    <span class = "like like_<?php echo $postID; ?>"><a href = "javascript: likeIt(<?php echo $postID; ?>);">Like</a></span>
                <?php endif; ?>
            <?php endif; ?>
        </span>
    <?php
    
}

function getLikesOnAuthorPosts( $userId ){
    global $wpdb;
    $query = "SELECT * FROM wp_posts WHERE post_type = 'topic' AND post_author = '$userId'";
    $results = $wpdb->get_results($query);
    
    $count = 0;
    
    foreach ($results as $each){
        $query2 = "SELECT * FROM wp_bbpress_likes WHERE post_id = '" . $each->ID . "'";
        $results2 = $wpdb->get_results($query2);
        
        $count = $count + count($results2);
        
    }
    
    echo $count;
}

function shortcodeCaller( $atts ){
    getBar_withLike($atts["postid"]);
}

function shortcodeCaller2( $atts ){
    getBar($atts["postid"]);
}

function shortcodeCaller3( $atts ){
    getLikesOnAuthorPosts($atts["author"]);
}

function shortcodeCaller4( $atts ){
    getMostLikedTopics($atts["qty"]);
}

add_shortcode( 'bbpressliketopics_withlike', 'shortcodeCaller' );
add_shortcode( 'bbpressliketopics', 'shortcodeCaller2' );
add_shortcode( 'bbpresslikesonauthor', 'shortcodeCaller3' );
add_shortcode( 'bbpressmostliked', 'shortcodeCaller4' );


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


/*
 * Ajax Support
 */
add_action('wp_enqueue_scripts', 'bbplt_ajax_handler');

function bbplt_ajax_handler() {
    
    wp_enqueue_script('bbplt_ajax', plugins_url("bbpress-like-topics") . '/js/ajax.js', array('jquery'));
    wp_localize_script('bbplt_ajax', 'bbplt_ajax', array(
        'ajaxurl' => admin_url('admin-ajax.php'))
    );
}

add_action('wp_ajax_likeIt', 'likeIt');
add_action('wp_ajax_nopriv_likeIt', 'likeIt');
function likeIt(){
    
    addToFavorites(get_current_user_id(), $_POST["post"]);
    echo getFavorites($_POST["post"]);
    exit;
}

add_action('wp_ajax_unlikeIt', 'unlikeIt');
add_action('wp_ajax_nopriv_unlikeIt', 'unlikeIt');
function unlikeIt(){
    
    removeFromFavorites(get_current_user_id(), $_POST["post"]);
    echo getFavorites($_POST["post"]);
    exit;
}

function checkIfHasAlreadyLiked($postId){
    
    $userId = get_current_user_id();
    global $wpdb;
    $query = "SELECT * FROM " .$wpdb->prefix . "bbpress_likes WHERE post_id = '$postId' AND user_id = '$userId'";
    $result = $wpdb->get_results($query);
    
    if (count($result) > 0){
        return 1;
    }else{
        return 0;
    }
    
}

function getMostLikedTopics(){
    
    global $wpdb;
    $query = "SELECT 
                likes.post_id, COUNT(likes.post_id) as count 
              FROM " .$wpdb->prefix . "bbpress_likes as likes
              INNER JOIN
                " .$wpdb->prefix . "posts as posts
              ON
                posts.id = likes.post_id
              WHERE 
                posts.post_type = 'topic'
              GROUP BY 
                likes.post_id 
              ORDER BY 
                count DESC";
    $result = $wpdb->get_results($query);
    ?>
    <ul>
        <?php foreach ($result as $post): ?>
            <li><a href="<?php echo get_permalink($post->post_id); ?>" title="<?php echo get_the_title($post->post_id); ?>"><?php echo get_the_title($post->post_id); ?></a></li>
        <?php endforeach; ?>
    </ul>
    <?php
}