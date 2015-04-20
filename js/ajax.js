function likeIt(postId){
    
    jQuery.ajax({
        url: bbplt_ajax.ajaxurl,
        type: "POST",
        data: ({
            action : 'likeIt',
            post: postId
        }),
        beforeSend: function(){
           
        },
        success: function(data) {
            jQuery(".counter_"+postId).html(data + " likes");
            jQuery(".like_"+postId).html("<a href = 'javascript: unlikeIt("+postId+")'>Unlike</a>");

        }
    });
}

function unlikeIt(postId){
    
    jQuery.ajax({
        url: bbplt_ajax.ajaxurl,
        type: "POST",
        data: ({
            action : 'unlikeIt',
            post: postId
        }),
        beforeSend: function(){
           
        },
        success: function(data) {
            jQuery(".counter_"+postId).html(data + " likes");
            jQuery(".like_"+postId).html("<a href = 'javascript: likeIt("+postId+")'>Like</a>");

        }
    });
}

