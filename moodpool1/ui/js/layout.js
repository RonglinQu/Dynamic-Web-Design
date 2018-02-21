function signuphidden(){
	if(document.getElementById("loginout").innerHTML=="logout"){
		document.getElementById('signup').style.visibility = 'hidden';
	}else{
		document.getElementById('signup').style.visibility = 'visible';
	}
}
