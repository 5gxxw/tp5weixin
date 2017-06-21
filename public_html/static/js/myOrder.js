/**
 * Created by Administrator on 2016/8/24 0024.
 */
$(function(){

    /*订单状态导航切换效果*/
    var _index = 0;
    $(".orderNav > li").click(function(){
        _index = $(this).index();
        $(this).addClass("click").siblings("li").stop(true).removeClass("click");
        $(".switchPool > .itemBox").eq(_index).addClass("show").siblings(".itemBox").stop(true).removeClass("show");
    });

    $(".switchPool > .itemBox > ul.itemUl > li > .btnBox >a.delete").click(function(){
        var $this = $(this);
        $.popup({
            type:2,
            color:'#E90327',
            text:"确认删除该商品吗？",
            yesFun:function(){
                $this.parents("li").remove();
            }
        });
    });

});