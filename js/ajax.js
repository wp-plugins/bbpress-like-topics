function likeIt(postId){
    
    $.ajax({
        url: bbplt_ajax.ajaxurl,
        type: "POST",
        data: ({
            action : 'likeIt',
            post: postId,
        }),
        beforeSend: function(){
           
        },
        success: function(data) {
            $(".counter#"+postId).html(data);
            $(".like_"+postId).html("<a href = 'javascript: unlikeIt("+postId+")'>Unlike</a>");

        }
    });
}

function unlikeIt(postId){
    
    $.ajax({
        url: bbplt_ajax.ajaxurl,
        type: "POST",
        data: ({
            action : 'unlikeIt',
            post: postId,
        }),
        beforeSend: function(){
           
        },
        success: function(data) {
            $(".counter#"+postId).html(data);
            $(".like_"+postId).html("<a href = 'javascript: likeIt("+postId+")'>Like</a>");

        }
    });
}

