requirejs.config({
  waitSeconds: 240,
   baseUrl: 'bhv/vendors',
   paths: {
     bhv: '..',
     app: '../../test',
     cms: '../../cms'
   },
   urlArgs: "bust=" + (new Date()).getTime(),
   map: {
     '*' : {'jquery': 'jquery-1.9.1'}
   }
});

requirejs(['bhv/widget/combobox/Combobox', 'domReady!'], function ( cmb ) {

combobox1 = new cmb({
  url: 'ci/index.php/cms/test/collection'
});
alert(combobox1.items.fetch)
combobox1.items.fetch({
data:{name:'qwerty'},

success:function(model,xhr,options){alert(JSON.stringify(xhr));model.clear;alert("1"+JSON.stringify(model.attributes));},
error:function(model,xhr,options){alert(xhr.responseText);alert("2"+JSON.stringify(model.attributes));}
}
);
alert(combobox1.items.fetch)

});



