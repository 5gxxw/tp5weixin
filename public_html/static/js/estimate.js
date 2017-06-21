/**
 * Created by Administrator on 2016/8/24 0024.
 */
$(function () {

    /*好评、中评、差评*/
    $(".estimate > dl > dd.estimateChoose > ul >li").click(function(){
        $(this).addClass("click").siblings("li").stop(true).removeClass("click");
    });

   

});