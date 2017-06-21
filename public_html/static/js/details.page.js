// JavaScript Document

$(function(){

	//禁止图片拖动
	$(".pop_bedimg .bedimg_field").attr("ondragstart","return false");

    $("a").attr('data-ajax',false);

	var count=0;
	var width = $(window).width();

	$(".bedimg_field ul li").css("width",width);


	$(".bedimg_field").bind('swipeleft',function(){
		var liLenght = $(this).find("li").length;
		// $(".pop_bedimg .begimg_num span.total").html(liLenght);
		if(count==liLenght-1){
			$(".pop_bedimg .bedimg_field .nomore_box").html("已经最后一张了");
			$(".pop_bedimg .bedimg_field .nomore_box").fadeIn();
			setTimeout(function(){
                //Initials.css('background','rgba(145,145,145,0)');
                $(".pop_bedimg .bedimg_field .nomore_box").fadeOut();
            },1000);

		}else{
			count++;
			$(".pop_bedimg .begimg_num span.current").html(count+1);
			$(".pop_bedimg ul").animate({"left":"-"+count*width+"px"},500);
		}
	}).bind('swiperight',function(){
        // var liLenght = $(this).find("li").length;
		// $(".pop_bedimg .begimg_num span.total").html(liLenght);
		if(count==0){
			$(".pop_bedimg .bedimg_field .nomore_box").html("已经第一张了");
			$(".pop_bedimg .bedimg_field .nomore_box").fadeIn();
			setTimeout(function(){
                //Initials.css('background','rgba(145,145,145,0)');
                $(".pop_bedimg .bedimg_field .nomore_box").fadeOut();
            },1000);
		}else{
			count--;
			$(".pop_bedimg .begimg_num span.current").html(count+1);
			$(".pop_bedimg ul").animate({"left":"-"+count*width+"px"},500);
		}
	});

	$(".pop_bedimg").click(function(){
		$(this).fadeOut();
        // $(".pop_bedimg .begimg_num span.current").html(1);
        // $(".pop_bedimg ul").animate({"left":"0"},500);
	});


	$(".bed_img img").click(function(){
        var liLenght = $(this).parents("li").find(".pop_bedimg .bedimg_field ul li").length;
        $(this).parents("li").find(".pop_bedimg .begimg_num span.total").html(liLenght);
		$(this).parents("li").find(".pop_bedimg").fadeIn();
        // $(".pop_bedimg .begimg_num span.current").html(1);
        // $(".pop_bedimg ul").animate({"left":"0"},500);
	});





	/*加载评分*/
 //        var star =$('.rating_icon').attr('star');
	// $('.rating_icon').raty({
	//   	number: 5,//多少个星星设置
	// 	score: star,//初始值是设置
	// 	targetType: 'number',//类型选择，number是数字值，hint，是设置的数组值
 //        path      : '/Public/Home/Images/',
 //        cancelOff : 'cancel-off-big.png',
 //        cancelOn  : 'cancel-on-big.png',
 //        size      : 26,
 //        starHalf  : 'star-half-big.png',
 //        starOff   : 'star-off-big.png',
 //        starOn    : 'star-on-big.png',
 //        target    : '.rating_far',
 //        cancel    : false,
 //        targetKeep: true,
 //        precision : true,//是否包含小数
 //        readOnly: true,//只读
 //        click: function(score, evt) {
 //          alert('ID: ' + $(this).attr('id') + "\nscore: " + score + "\nevent: " + evt.type);
 //        }
 //    });   

});

