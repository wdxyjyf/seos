$(function(){
    $.post("/home/common/push", {url:location.href}, function(res){
    	console.log(location.href);
  		console.log(res)
    }, 'json') 
});