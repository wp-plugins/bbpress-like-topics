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
            $(".likes_bbpress .counter").html(data);
            $(".likes_bbpress .like").html("Liked");
        }
    });
}
