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

requirejs(['bhv/widget/combobox/BBCombobox', 'domReady!'], function ( cmb ) {

combobox1 = new cmb({
  parent: 'combo1',
  url: 'test_pdo.php',
  keyName: 'kod',
  searchName: 'name',
  displayName: 'det',
  store: 'input1'
});
alert('hi')
alert(combobox1.items.length)
//combobox1.items.add({id:162})
//combobox1.items.add({id:165})
//combobox1.input.set('searchValue', 'класс');
//combobox1.items.at(0).set('ttttttttttttt', "5555555555555555")
//combobox1.read();
combobox1.setValue(65181);
//combobox1.setValue(25);
combobox2 = new cmb({
  parent: 'combo2',
  url: 'test_pdo.php',
  keyName: 'kod',
  searchName: 'name',
  displayName: 'det',
  POFF20: true
});


/*combobox1.fetch( {
data:{name:'qwerty'},

success:function(model,xhr,options){alert("1"+JSON.stringify(xhr))},
error:function(model,xhr,options){alert(2)}
} 
);*/

});



