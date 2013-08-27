define( ['cms/cms','ci/index.php/cms/login/birsa?srand='+Math.random()], function( cms, rsa ) {

/*var keye = new rsa('5abb','0','1d7777c38863aec21ba2d91ee0faf51')
var keyd = new rsa('0','1146bd07f0b74c086df00b37c602a0b','1d7777c38863aec21ba2d91ee0faf51')
var Login = Backbone.Model.extend({
  urlRoot: cms.bhv.getApplicationFolder() + 'ci/index.php/cms/login/model',
  toJSON: function() {
    var attrs = {};
    attrs.name = this.get('name');
    attrs.email = this.get('email');
    attrs.encryptedpassword = keye.encrypt(rsa.salt + this.get('password'));
    alert("3"+(attrs.decryptedpassword = keyd.decrypt(attrs.encryptedpassword)));
    
    attrs.rand = Math.random()
    return attrs;
  },
  validate: function() {
  }
});


var login = new Login({id:'', name:'Джон-2', password:'русский текст русский текст', email:'john@home.org'})
Login.prototype.on('invalid', function(model, error){
  alert('onerror' + JSON.stringify( error ) );
})
login.save({_id:'Joe'},{
success:function(model,xhr,options){alert(JSON.stringify(xhr));alert("1"+JSON.stringify(model.attributes));},
error:function(model,xhr,options){alert(xhr.responseText);alert("2"+JSON.stringify(model.attributes));}}
);*/

O1=Backbone.Model.extend({a1:1})
O2=Backbone.Model.extend({a2:2})
o1=new O1()
o2=new O2()



var Test =  Backbone.Model.extend({
  urlRoot: cms.bhv.getApplicationFolder() + 'ci/index.php/cms/test/model',
  destroy: function(){ this.constructor.__super__.destroy.apply(this, arguments)}
});

test = new Test({id:'qwerty6',email:'test3@gmail.com', password:'русский текст',name:'qwerty'});

/*test.save ({}, {wait:true,
success:function(model,xhr,options){alert(JSON.stringify(xhr));model.clear;alert("1"+JSON.stringify(model.attributes));},
error:function(model,xhr,options){alert(xhr.responseText);alert("2"+JSON.stringify(model.attributes));}}
);

return
alert(0)

test.fetch ( {
success:function(model,xhr,options){alert(JSON.stringify(xhr));model.clear;alert("1"+JSON.stringify(model.attributes));},
error:function(model,xhr,options){alert(xhr.responseText);alert("2"+JSON.stringify(model.attributes));}}
);

return
alert(0)*/

test.save ({}, {
success:function(model,xhr,options){alert(JSON.stringify(xhr));alert("1"+JSON.stringify(model.attributes));},
error:function(model,xhr,options){alert(xhr.responseText);alert("2"+JSON.stringify(model.attributes));}}
);
return
alert(0)

test.destroy ( {wait:true,
success:function(model,xhr,options){alert(JSON.stringify(xhr));model.clear;alert("1"+JSON.stringify(model.attributes));},
error:function(model,xhr,options){alert(xhr.responseText);alert("2"+JSON.stringify(model.attributes));}}
);

})