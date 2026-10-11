/*
仿2345导航模版js
小麦科技 xmkj
 */
var elem = document.getElementById('my-Swipe');
window.mySwipe = Swipe(elem, {
  continuous: false,
  callback: function(index, element) {
	$('#ifeng-news-button li').eq(index).addClass('cur');
	$('#ifeng-news-button li').eq(index).siblings().removeClass('cur');
  },
});
$('#ifeng-news-button li').each(function(){
		$(this).click(function(){
			mySwipe.slide($(this).index());
		 })															
});
