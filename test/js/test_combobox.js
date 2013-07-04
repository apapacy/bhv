requirejs.config({
   baseUrl: '../bhv/vendors',
   paths: {
     bhv: '..',
     app: '../../test'
   },
   urlArgs: "bust=" + (new Date()).getTime(),
   map: {
     '*' : {'jquery': 'jquery-1.9.1'}
   }
});

requirejs(['jquery', 'bhv/util', 'bhv/classes', 'bhv/widget/Combobox', 'domReady!'],
function (jQ, util, classes, cmbc) {
function main(){
classes.create(cmbc, "combobox1", "comb1", 2,20,"cennic","kod","name","det")
classes.create(cmbc, "combobox2","comb2",19,20,"cennic","kod","name","det");
var cp = document.getElementById("combopane")
for (var i=3; i<20; i++){
  void function(){  
  var i0
  i0=i
  var sp = document.createElement("SPAN")
  sp.id="id"+i
  cp.appendChild(sp)
  window.setTimeout(function(){classes.create(cmbc, sp, undefined, i0,20,"cennic","kod","name","det");},100)
  }()
}
}
main();
});