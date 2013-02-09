var generator = new Object();
generator.params = new Object();
generator.updateParam = function(name, value)
{
	//var element = document.getElementById(id);
	switch (this.params[name].type) {
		case "url":
		case "text":
		case "color":
			this.params[name].value = value;
			break;
		case "int":
			this.params[name].value = Number(value);
			break;
		case "bool":
			this.params[name].value = value; // (value == "on")?1:0;
			break;
	}
}

generator.addParam = function(id, name, type, defaultValue)
{
	var element = document.getElementById(id);
	this.params[name] = new Object();
	this.params[name].type = type;
	this.params[name].defaultValue = defaultValue;
	this.params[name].element = element;
	switch (type) {
		case "url":
		case "text":
			this.params[name].value = element.value;
			element.onchange = delegate(this.params[name], function()
			{
				this.value = this.element.value;
				generator.updatePlayer();
			});
			break;
		case "color":
			this.params[name].value = element.value.replace(/#/, '');
			element.onchange = delegate(this.params[name], function()
			{
				this.value = this.element.value;
				generator.updatePlayer();
			});
			break;
			break;
		case "int":
			this.params[name].value = Number(element.value);
			element.onchange = delegate(this.params[name], function()
			{
				this.value = Number(this.element.value);
				generator.updatePlayer();
			});
			break;
		case "bool":
			this.params[name].value = element.value; // (element.value == "on")?1:0;
			element.onchange = delegate(this.params[name], function()
			{
				this.value = this.element.value; // (this.element.value == "on")?1:0;
				generator.updatePlayer();
			});
			break;
	}
};
generator.updatePlayer = function()
{
	var out = '<object type="application/x-shockwave-flash" data="'+this.player+'" width="'+this.params.width.value+'" height="'+this.params.height.value+'">'+"\n";
	out += '    <param name="movie" value="'+this.player+'" />'+"\n";
	if (this.params.bgcolor) {
		out += '    <param name="bgcolor" value="#'+this.params.bgcolor.value.replace(/#/, '')+'" />'+"\n";
	}
	out += '    <param name="FlashVars" value="';
	
	var separator = '';
	for (var i in this.params) {
		if (this.params[i].value != this.params[i].defaultValue && this.params[i].value != '' ) {
			if (this.params[i].type == "url") {
				out += separator + i + '=' + escape(this.params[i].value);
			} 
			else if(this.params[i].type == "color") {
				out += separator + i + '=' + this.params[i].value.replace(/#/, '');
			}
			else {
				out += separator + i + '=' + escapeHTML(this.params[i].value);
			}
			separator = '&amp;';
		}
	}
	
	out += '" />'+"\n";
	out += '</object>';
	
	var player = document.getElementById("player_preview");
	player.innerHTML = out;
};

var params = new Object();

/* =============== UTILS =============== */
var delegate = function(pTarget, pFunction)
{
	var f = function(){
		arguments.callee.func.apply(arguments.callee.target, arguments);
	};
	f.target = pTarget;
	f.func = pFunction;
	return f;
}
var escapeHTML = function(str) {
    str = String(str);
    str = str.replace(/&/gi, '');
    
    var div = document.createElement("div");
    var text = document.createTextNode('');
    div.appendChild(text);
    text.data = str;
    
    var result = div.innerHTML;
    result = result.replace(/"/gi, '&quot;');
    
    return result;
}
var findPosX = function (obj)
{
	var curleft = 0;
	do {
		curleft += obj.offsetLeft || 0;
		obj = obj.offsetParent;
	} while (obj);
	return curleft;
};
var findPosY = function (obj)
{
	var curtop = 0;
	do {
		curtop += obj.offsetTop || 0;
		obj = obj.offsetParent;
	} while (obj);
	return curtop;
};
var twoChar = function (str)
{
	if (str.length == 1) {
		return "0" + str;
	}
	return str;
};
