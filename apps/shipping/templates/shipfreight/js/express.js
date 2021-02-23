/* $Id : utils.js 5052 2007-02-03 10:30:13Z weberliu $ */

var Browser = new Object();

Browser.isMozilla = (typeof document.implementation != 'undefined') && (typeof document.implementation.createDocument != 'undefined') && (typeof HTMLDocument != 'undefined');
Browser.isIE = window.ActiveXObject ? true : false;
Browser.isFirefox = (navigator.userAgent.toLowerCase().indexOf("firefox") != - 1);
Browser.isSafari = (navigator.userAgent.toLowerCase().indexOf("safari") != - 1);
Browser.isOpera = (navigator.userAgent.toLowerCase().indexOf("opera") != - 1);

var Utils = new Object();

Utils.htmlEncode = function(text)
{
  return text.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

Utils.trim = function( text )
{
  if (typeof(text) == "string")
  {
    return text.replace(/^\s*|\s*$/g, "");
  }
  else
  {
    return text;
  }
}

Utils.isEmpty = function( val )
{
  switch (typeof(val))
  {
    case 'string':
      return Utils.trim(val).length == 0 ? true : false;
      break;
    case 'number':
      return val == 0;
      break;
    case 'object':
      return val == null;
      break;
    case 'array':
      return val.length == 0;
      break;
    default:
      return true;
  }
}

Utils.isNumber = function(val)
{
  var reg = /[\d|\.|,]+/;
  return reg.test(val);
}

Utils.isInt = function(val)
{
  var reg = /\d+/;
  return reg.test(val);
}

Utils.isEmail = function( email )
{
  var reg1 = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/;

  return reg1.test( email );
}

Utils.fixEvent = function(e)
{
  var evt = (typeof e == "undefined") ? window.event : e;
  return evt;
}

Utils.srcElement = function(e)
{
  if (typeof e == "undefined") e = window.event;
  var src = document.all ? e.srcElement : e.target;

  return src;
}

Utils.isTime = function(val)
{
  var reg = /^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/;

  return reg.test(val);
}

Utils.strlenReg = function(str)
{
	return str.replace(/[^x00-xFF]/g,'**').length;
}

function rowindex(tr)
{
  if (Browser.isIE)
  {
    return tr.rowIndex;
  }
  else
  {
    table = tr.parentNode.parentNode;
    for (i = 0; i < table.rows.length; i ++ )
    {
      if (table.rows[i] == tr)
      {
        return i;
      }
    }
  }
}

document.getCookie = function(sName)
{
  // cookies are separated by semicolons
  var aCookie = document.cookie.split("; ");
  for (var i=0; i < aCookie.length; i++)
  {
    // a name/value pair (a crumb) is separated by an equal sign
    var aCrumb = aCookie[i].split("=");
    if (sName == aCrumb[0])
      return decodeURI(aCrumb[1]);
  }

  // a cookie with the requested name does not exist
  return null;
}

document.setCookie = function(sName, sValue, sExpires)
{
  var sCookie = sName + "=" + encodeURIComponent(sValue);

  if (sExpires != null)
  {
    sCookie += "; expires=" + sExpires;
  }

  document.cookie = sCookie;
}

document.removeCookie = function(sName)
{
  document.cookie = sName + "=" + escape(sValue) + "; expires=Fri, 31 Dec 1999 23:59:59 GMT;";
}

function DrawImg(ImgD , FitWidth , FitHeight)
{   
 //var ImgD = document.getElementById('main_pic');
 //alert(typeof(ImgD));
 var image 	= 	new Image();  
 image.src	=	ImgD.src;  
 if(image.width>0 && image.height>0)   
 {   
	 if(image.width/image.height>= FitWidth/FitHeight)   
	 {   
		 if(image.width>FitWidth)   
		 {   
			 ImgD.width=FitWidth;   
			 ImgD.height=(image.height*FitWidth)/image.width;   
		 }   
		 else   
		 {   
			 ImgD.width=image.width;   
			 ImgD.height=image.height;   
		 }   
	 }   
	 else   
	 {   
		 if(image.height>FitHeight)   
		 {   
			 ImgD.height=FitHeight;   
			 ImgD.width=(image.width*FitHeight)/image.height;   
		 }   
		 else   
		 {   
			 ImgD.width=image.width;   
			 ImgD.height=image.height;   
		 }   
	 }   
 }
 
 //���ô�ֱ����
 ImgD.style.marginTop	=	((FitHeight - ImgD.height) / 2) + "px";
} 
