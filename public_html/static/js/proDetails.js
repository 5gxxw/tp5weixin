/**
 * Created by Administrator on 2016/8/18 0018.
 */
$(function () {

    // 取消jquery.mobile的loading
    $(".ui-loader").remove();
    $("a").attr('data-ajax',false);





    /*商品详情与评论切换*/
    var _index = 0;

    $(".switchNav > ul > li").click(function(){
        _index = $(this).index();
        $(this).addClass("click").siblings("li").stop(true).removeClass("click");
        $(".switchPool > .item").eq(_index).addClass("show").siblings(".item").stop(true).removeClass("show");
    });
    /*评论中的导航*/
    $(".estimate > .nav >li").click(function(){
        _index = $(this).index();
        $(this).addClass("click").siblings("li").stop(true).removeClass("click");
        $(".estimate > .switchPond > ul.detailItem").eq(_index).addClass("show").siblings("ul.detailItem").removeClass("show");
    });

   

    


});


