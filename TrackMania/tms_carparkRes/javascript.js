var _____WB$wombat$assign$function_____ = function(name) {return (self._wb_wombat && self._wb_wombat.local_init && self._wb_wombat.local_init(name)) || self[name]; };
if (!self.__WB_pmw) { self.__WB_pmw = function(obj) { this.__WB_source = obj; return this; } }
{
  let window = _____WB$wombat$assign$function_____("window");
  let self = _____WB$wombat$assign$function_____("self");
  let document = _____WB$wombat$assign$function_____("document");
  let location = _____WB$wombat$assign$function_____("location");
  let top = _____WB$wombat$assign$function_____("top");
  let parent = _____WB$wombat$assign$function_____("parent");
  let frames = _____WB$wombat$assign$function_____("frames");
  let opener = _____WB$wombat$assign$function_____("opener");

// JavaScript Document

// Startup variables
var imageTag = false;
var theSelection = false;

// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;

var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);

// Helpline messages
b_help = "Texte gras: [b]texte[/b]";
i_help = "Texte italique: [i]texte[/i]";
u_help = "Texte souligné: [u]texte[/u]";
q_help = "Citation: [quote]texte cité[/quote]";
c_help = "Afficher du code: [code]code[/code]";
l_help = "Liste: [list][*]Option[/list] (alt+l)";
o_help = "Liste ordonnée: [list=]texte[/list]";
p_help = "Insérer une image: [img]http://image_url/[/img]";
w_help = "Insérer un lien: [url=http://url/]Ton Texte[/url]";
a_help = "Fermer toutes les balises BBCode ouvertes";
s_help = "Couleur du texte: [color=red]texte[/color]";
f_help = "Taille du texte: [size=x-small]texte en petit[/size]";

// Define the bbCode tags
bbcode = new Array();
bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[quote]','[/quote]','[code]','[/code]','[list]','[/list]','[list=]','[/list]','[img]','[/img]','[url]','[/url]');
imageTag = false;

// Shows the help messages in the helpline window
function helpline(help) {
	document.upload.helpbox.value = eval(help + "_help");
}


// Replacement for arrayname.length property
function getarraysize(thearray) {
	for (i = 0; i < thearray.length; i++) {
		if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
			return i;
		}
	return thearray.length;
}

// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray,value) {
	thearray[ getarraysize(thearray) ] = value;
}

// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
	thearraysize = getarraysize(thearray);
	retval = thearray[thearraysize - 1];
	delete thearray[thearraysize - 1];
	return retval;
}


function checkForm() {

	formErrors = false;

	if (document.upload.texte.value.length < 2) {
		formErrors = "Vous devez entrer un message avant de poster.";
	}

	if (formErrors) {
		alert(formErrors);
		return false;
	} else {
		bbstyle(-1);
		//formObj.preview.disabled = true;
		//formObj.submit.disabled = true;
		return true;
	}
}

function emoticon(text) {
	var txtarea = document.upload.texte;
	text = ' ' + text + ' ';
	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
	} else {
		txtarea.value  += text;
		txtarea.focus();
	}
}

function bbfontstyle(bbopen, bbclose) {
	var txtarea = document.upload.texte;

	if ((clientVer >= 4) && is_ie && is_win) {
		theSelection = document.selection.createRange().text;
		if (!theSelection) {
			txtarea.value += bbopen + bbclose;
			txtarea.focus();
			return;
		}
		document.selection.createRange().text = bbopen + theSelection + bbclose;
		txtarea.focus();
		return;
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, bbopen, bbclose);
		return;
	}
	else
	{
		txtarea.value += bbopen + bbclose;
		txtarea.focus();
	}
	storeCaret(txtarea);
}


function bbstyle(bbnumber) {
	var txtarea = document.upload.texte;

	txtarea.focus();
	donotinsert = false;
	theSelection = false;
	bblast = 0;

	if (bbnumber == -1) { // Close all open tags & default button names
		while (bbcode[0]) {
			butnumber = arraypop(bbcode) - 1;
			txtarea.value += bbtags[butnumber + 1];
			buttext = eval('document.upload.addbbcode' + butnumber + '.value');
			eval('document.upload.addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
		}
		imageTag = false; // All tags are closed including image tags :D
		txtarea.focus();
		return;
	}

	if ((clientVer >= 4) && is_ie && is_win)
	{
		theSelection = document.selection.createRange().text; // Get text selection
		if (theSelection) {
			// Add tags around selection
			document.selection.createRange().text = bbtags[bbnumber] + theSelection + bbtags[bbnumber+1];
			txtarea.focus();
			theSelection = '';
			return;
		}
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, bbtags[bbnumber], bbtags[bbnumber+1]);
		return;
	}

	// Find last occurance of an open tag the same as the one just clicked
	for (i = 0; i < bbcode.length; i++) {
		if (bbcode[i] == bbnumber+1) {
			bblast = i;
			donotinsert = true;
		}
	}

	if (donotinsert) {		// Close all open tags up to the one just clicked & default button names
		while (bbcode[bblast]) {
				butnumber = arraypop(bbcode) - 1;
				txtarea.value += bbtags[butnumber + 1];
				buttext = eval('document.upload.addbbcode' + butnumber + '.value');
				eval('document.upload.addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
				imageTag = false;
			}
			txtarea.focus();
			return;
	} else { // Open tags

		if (imageTag && (bbnumber != 14)) {		// Close image tag before adding another
			txtarea.value += bbtags[15];
			lastValue = arraypop(bbcode) - 1;	// Remove the close image tag from the list
			document.upload.addbbcode14.value = "Img";	// Return button back to normal state
			imageTag = false;
		}

		// Open tag
		txtarea.value += bbtags[bbnumber];
		if ((bbnumber == 14) && (imageTag == false)) imageTag = 1; // Check to stop additional tags after an unclosed image tag
		arraypush(bbcode,bbnumber+1);
		eval('document.upload.addbbcode'+bbnumber+'.value += "*"');
		txtarea.focus();
		return;
	}
	storeCaret(txtarea);
}

// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close)
{
	var selLength = txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	if (selEnd == 1 || selEnd == 2)
		selEnd = selLength;

	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);
	txtarea.value = s1 + open + s2 + close + s3;
	return;
}

// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}


function tag_list()
{
	var listvalue = "init";
	var thelist = "";
	
	while ( (listvalue != "") && (listvalue != null) )
	{
		listvalue = prompt("Saisissez un objet de liste. Cliquez sur 'annuler' ou laissez à blanc pour terminer la liste", "");
		if ( (listvalue != "") && (listvalue != null) )
		{
			thelist = thelist+"[*]"+listvalue+"\n";
		}
	}
	

	if ( thelist != "" )
	{
		document.upload.texte.value += "[LIST]\n" + thelist + "[/LIST]\n";
	}
}

function menuMontre() {	
	if (document.getElementById('sousMenu') != null) {
		document.getElementById('sousMenu').style.display="block";
	} else if (document.all['sousMenu'] != null) {
		document.all['sousMenu'].style.display="block";
	} else if (document.layers['sousMenu'] != null) {
		document.layers['sousMenu'].display="block";
	}
}

function menuCache() {
	if (document.getElementById('sousMenu') != null) {
		document.getElementById('sousMenu').style.display="none";
	} else if (document.all['sousMenu'] != null) {
		document.all['sousMenu'].style.display="none";
	} else if (document.layers['sousMenu'] != null) {
		document.layers['sousMenu'].display="none";
	}
}


function gameCompatibilities() {
	var jeu = document.getElementById('game');
	var span_tmn = document.getElementById('span_tmn');
	var span_tmo = document.getElementById('span_tmo');
	var span_tms = document.getElementById('span_tms');
	var tmn_compatible = document.getElementById('tmn_compatible');
	var tmo_compatible = document.getElementById('tmo_compatible');
	var tms_compatible = document.getElementById('tms_compatible');
	
	if(span_tmn != null && span_tms != null && span_tmo != null){
		if(jeu.value == 'tms'){
			span_tmn.style.display = 'block';	
			span_tmo.style.display = 'block';
			span_tms.style.display = 'none';
			tmn_compatible.style.display = 'block';	
			tmo_compatible.style.display = 'block';
			tms_compatible.style.display = 'none';
		}else if(jeu.value == 'tm'){
			span_tmn.style.display = 'block';	
			span_tmo.style.display = 'none';
			span_tms.style.display = 'block';	
			tmn_compatible.style.display = 'block';	
			tmo_compatible.style.display = 'none';
			tms_compatible.style.display = 'block';
		}else if(jeu.value == 'tmn'){
			span_tmn.style.display = 'none';	
			span_tmo.style.display = 'block';
			span_tms.style.display = 'block';	
			tmn_compatible.style.display = 'none';	
			tmo_compatible.style.display = 'block';
			tms_compatible.style.display = 'block';
		}
	}
}
//-->

}
/*
     FILE ARCHIVED ON 13:05:34 Jun 18, 2006 AND RETRIEVED FROM THE
     INTERNET ARCHIVE ON 13:03:01 Jan 19, 2023.
     JAVASCRIPT APPENDED BY WAYBACK MACHINE, COPYRIGHT INTERNET ARCHIVE.

     ALL OTHER CONTENT MAY ALSO BE PROTECTED BY COPYRIGHT (17 U.S.C.
     SECTION 108(a)(3)).
*/
/*
playback timings (ms):
  captures_list: 143.033
  exclusion.robots: 0.134
  exclusion.robots.policy: 0.124
  cdx.remote: 0.094
  esindex: 0.011
  LoadShardBlock: 103.865 (3)
  PetaboxLoader3.datanode: 122.746 (4)
  CDXLines.iter: 23.152 (3)
  load_resource: 79.722
  PetaboxLoader3.resolve: 23.652
*/