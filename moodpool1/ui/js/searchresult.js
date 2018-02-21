
function passInf(key,base_path){
	if(document.getElementById("button".concat(key)).innerHTML=="like"){	
		var moodtag = document.getElementById("moodtag").innerHTML;
		var img = document.getElementById("img".concat(key)).href;
		var url = document.getElementById("url".concat(key)).href;
		var title = document.getElementById("title".concat(key)).innerHTML;
		var iteminfo = JSON.stringify({ moodtag: moodtag, img: img,url:url,title:title });
		var response = new Array();
		var path = base_path.concat('/like');
		$.ajax(
			{ 
				type: "POST", 
				url: path, 
				data: { imageinfo : iteminfo },					
				success: function(response) { 
					var res = $.parseJSON(response);
					if(res.result != 1){
						$("#response").html(res.message);
					}else{
						document.getElementById("button".concat(key)).innerHTML = "unlike";
						document.getElementById("button".concat(key)).value = res.message;
					}
				}
			}
		);
	}else{
		var mediaid = document.getElementById("button".concat(key)).value;
		var iteminfo = JSON.stringify({ mediaid: mediaid});
		var path = base_path.concat('/unlike');
		$.ajax(
			{ 
				type: "POST", 
				url: path, 
				data: { mediaid : iteminfo }, 
				success: function(response) { 
					if(response != 1){
						$("#response").html(response);
					}else{
						document.getElementById("button".concat(key)).innerHTML = "like";
					}
				}
			}
		);
	}
}
function liked(key) {
	if(document.getElementById("button".concat(key)).innerHTML==true){
		document.getElementById("button".concat(key)).innerHTML = "unlike";
	}else{
		document.getElementById("button".concat(key)).innerHTML = "like";
	}	
}
