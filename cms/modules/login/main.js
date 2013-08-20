define( ['cms/cms','ci/index.php/cms/login/birsa?php=1'], function( cms, rsa ) {

var keye = new rsa('5abb','0','1d7777c38863aec21ba2d91ee0faf51')
var keyd = new rsa('0','1146bd07f0b74c086df00b37c602a0b','1d7777c38863aec21ba2d91ee0faf51')
var Login = cms.bb.Model.extend({
  urlRoot: cms.bhv.getApplicationFolder() + 'ci/index.php/cms/login/model',
  toJSON: function() {
    var attrs = {};
    attrs.name = this.get('name');
    attrs.email = this.get('email');
    attrs.encryptedpassword = keye.encrypt(rsa.salt + this.get('password'));
    attrs.decryptedpassword = keyd.decrypt(attrs.encryptedpassword);
    attrs.rand = Math.random()
    return attrs;
  }
});

var login = new Login({_id:'', name:'Джон', password:'-♪♫', email:'john@home.org'})
login.save({_id:'Joe'},{
success:function(model,xhr,options){alert(JSON.stringify(xhr));alert(JSON.stringify(model.attributes));},
error:function(model,xhr,options){alert(xhr.responseText);alert(JSON.stringify(model.attributes));}}
);
/*alert (rsa)
//m = 1d7777c38863aec21ba2d91ee0faf51
//e = 5abb
//d = 1146bd07f0b74c086df00b37c602a0b
//var key = new rsa('5abb','1146bd07f0b74c086df00b37c602a0b','1d7777c38863aec21ba2d91ee0faf51')
var str = 'однажды в студеную зимнюю пору я из лесу вышел был сильный мороз';
var time = Date.now()
str = keye.encrypt(str)
time -= Date.now()
alert(str+time)
time = Date.now()
str = keyd.decrypt(str)
time -= Date.now()
alert(str+time)*/













})