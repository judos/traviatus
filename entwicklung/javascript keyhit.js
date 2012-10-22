function keyDown(event){
	var event,keyCode;
	if (!event)
		event = window.event;
	if (event.which) {
		keyCode = event.which;
	} else if (event.keyCode) {
		keyCode = event.keyCode;
	}
	alert("Taste mit Dezimalwert " + keyCode + " gedrückt");
}
document.onkeydown=keyDown;