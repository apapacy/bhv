var bhv = {util:{}};

if (typeof document.getElementsByTagName == 'undefined')
	document.getElementsByTagName = function(tagname){return document.all.tags(tagname);};

bhv.APPLICATION_FOLDER = null;

bhv.getApplicationFolder = function(){
  if (bhv.APPLICATION_FODER)
    return bhv.APPLICATION_FOLDER;

  var scripts = document.getElementsByTagName("SCRIPT");
  var indexOfRoot = -1;
  for (var i = 0; i < scripts.length; i++) {
    indexOfRoot = String(scripts[i].src).replace(/\\/g,'/').lastIndexOf('bhv/util.js');
    if (indexOfRoot >= 0){
        bhv.APPLICATION_FOLDER = new String(scripts[i].src).substring(0, indexOfRoot)
        return bhv.APPLICATION_FOLDER;
    }
  }
}

/*bhv.getApplicationFolder = function(){
  if (bhv.APPLICATION_FODER)
    return bhv.APPLICATION_FOLDER;
	if (typeof document.getElementById != "undefined")
		var script = document.getElementById("bhv_util_script");
	else
		var script = window.bhv_util_script;
	var indexOfRoot = -1;
  indexOfRoot = String(script.src).replace(/\\/g,'/').lastIndexOf('bhv/util.js');
    if (indexOfRoot >= 0){
        bhv.APPLICATION_FOLDER = new String(script.src).substring(0, indexOfRoot)
        return bhv.APPLICATION_FOLDER;
    }
}*/

if (typeof document.getElementById != 'undefined'){
	bhv.IE4 = false;
	document.write('<script src="'+bhv.getApplicationFolder()+'bhv/util5ie.js'+'?rand='+Math.random()+'"></script>');
	bhv.getElementById = function(s){return document.getElementById(s)};
}else{
	bhv.IE4 = true;
	document.write('<script src="'+bhv.getApplicationFolder()+'bhv/util4ie.js'+'?rand='+Math.random()+'"></script>'); // notry version
	document.getElementById = bhv.getElementById = function(s){return document.all[s]};
}
document.write('<style type="text/css">div, span {border-width: 0px; border-style: none; padding: 0px; margin: 0px}</style>');
document.write('<link rel=stylesheet type="text/css" href="'+bhv.getApplicationFolder()+'combobox/combobox.css'+'?rand='+Math.random()+'"></script>');
document.write('<script src="'+bhv.getApplicationFolder()+'combobox/Combobox.js'+'?rand='+Math.random()+'"></script>');
document.write('<script src="'+bhv.getApplicationFolder()+'combobox/Combotree.js'+'?rand='+Math.random()+'"></script>');