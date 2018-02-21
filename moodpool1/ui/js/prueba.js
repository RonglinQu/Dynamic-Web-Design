$(document).ready(function(){
	$(".category_item").click(function(){
		var category = $(this).attr("id");
	
		if(category === "all"){
			$(".mood_item").addClass("hide");	
			setTimeout(function(){
				$(".mood_item").removeClass("hide");
			}, 300);
		} else {
			$(".mood_item").addClass("hide");	
			setTimeout(function(){
				$("." + category).removeClass("hide");
			}, 300);	
			
		}
	});
});