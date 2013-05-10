/*
    http://alice-grafixx.de/JavaScript-Tutorial/Auf/Zu-klappen-159
*/

function toggle(control){
	var elem = document.getElementById(control);
	
	if(elem.style.display == "none"){
		elem.style.display = "block";
	}else{
		elem.style.display = "none";
	}
}
