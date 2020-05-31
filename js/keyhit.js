//The following does not work since inside a textfield you have to enter numbers
//to build troops and so on, and this will also switch page using the following code.
//solution see: http://stackoverflow.com/questions/6618289/check-if-focus-is-on-a-textfield
/*function keydown(code) {
	if (code==VK_1)
		window.location.href='?page=dorf1';
	if (code==VK_2)
		window.location.href='?page=dorf2';
	if (code==VK_3)
		window.location.href='?page=karte';
	if (code==VK_4)
		window.location.href='?page=statistiken';
	if (code==VK_5)
		window.location.href='?page=berichte';
	if (code==VK_6)
		window.location.href='?page=nachrichten';
}*/


var VK_ESC = 27;
var VK_0   = 48;
var VK_1   = 49;
var VK_2   = 50;
var VK_3   = 51;
var VK_4   = 52;
var VK_5   = 53;
var VK_6   = 54;
var VK_7   = 55;
var VK_8   = 56;
var VK_9   = 57;
var VK_APOSTROPH = 222;
var VK_CIRCUMFLEX = 160;
var VK_BACKSPACE = 8;
var VK_TAB = 9;


var VK_Q = 81;
var VK_W = 87;
var VK_E = 69;
var VK_R = 82;
var VK_T = 84;
var VK_Z = 90;
var VK_U = 85;
var VK_I = 73;
var VK_O = 79;
var VK_P = 80;

var VK_EXCLAMATION = 161;
var VK_ENTER = 13;
var VK_CTRL = 17; //left and right has same code
//var VK_Ü = undefined;

var VK_A = 65;
var VK_S = 83;
var VK_D = 68;
var VK_F = 70;
var VK_G = 71;
var VK_H = 72;
var VK_J = 74;
var VK_K = 75;
var VK_L = 76;

//var VK_Ö = undefined;
//var VK_Ä = undefined;
//var VK_°§ = undefined;
var VK_SHIFT = 16; //left and right has same code
var VK_DOLLAR = 164; //#

var VK_Y = 89;
var VK_X = 88;
var VK_C = 67;
var VK_V = 86;
var VK_B = 66;
var VK_N = 78;
var VK_M = 77;
var VK_COMMA = 188;
var VK_POINT = 190;
var VK_MINUS = 173;

var VK_STAR = 106;
var VK_ALT = 18;
var VK_SPACE  = 32;
var VK_CAPS_LOCK = 20;

var VK_F1 = 112;
var VK_F2 = 113;
var VK_F3 = 114;
var VK_F4 = 115;
var VK_F5 = 116;
var VK_F6 = 117;
var VK_F7 = 118;
var VK_F8 = 119;
var VK_F9 = 120;
var VK_F10 = 121;

var VK_NUM_LOCK = 144;
var VK_SCROLL_LOCK = 145
var VK_NUM7 = 103;
var VK_NUM8 = 104;
var VK_NUM9 = 105;
var VK_NUM_MINUS = 109;
var VK_NUM4 = 100;
var VK_NUM5 = 101;
var VK_NUM6 = 102;
var VK_PLUS = 107;
var VK_NUM1 = 97;
var VK_NUM2 = 98;
var VK_NUM3 = 99;
var VK_NUM0 = 96;
var VK_NUM_COMMA = 110;

var VK_SMALLER = 60;
var VK_F11 = 122;
var VK_F12 = 123;

var VK_LEFT = 37;
var VK_UP = 38;
var VK_RIGHT = 39;
var VK_DOWN = 40;

var scancodes1 = [27,49,50,51,52,53,54,55,56,57,48,222,160,8,
	9,81,87,69,82,84,90,85,73,79,80,-1,161,13,17,
	65,83,68,70,71,72,74,75,76,-1,-1,-1,16,164,
	89,88,67,86,66,78,77,188,190,173,
	16,106,18,32,20,
	112,113,114,115,116,117,118,119,120,121,
	144,145,103,104,105,109,100,101,102,107,97,98,99,96,
	110,-2,-2,60,122,123];
scancodes1[200] = 38;
scancodes1[203] = 37;
scancodes1[205] = 39;
scancodes1[208] = 40;

//scancodes missing for these:
var VK_HOME = 36;
var VK_PAGE_DOWN = 33;
var VK_PAGE_UP = 34;
var VK_END = 35;


function keyCode2Scancode(keyCode) {
	var i=scancodes1.indexOf(keyCode)+1;
	return i;
}

function keydown_construct(event){
	var event,keyCode;
	if (!event)
		event = window.event;
	if (event.which) {
		keyCode = event.which;
	} else if (event.keyCode) {
		keyCode = event.keyCode;
	}
	if (typeof(keydown)==typeof(Function)){
		keydown(keyCode);
	}
}
document.onkeydown=keydown_construct;