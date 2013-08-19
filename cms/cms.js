define(['bhv/util', 'bhv/classes', 'backbone', 'domReady'],

function (util, classes, us, bb) {
alert(1)
var cms = new Object()
cms.bhv = cms.util = util;
cms.classes = classes;
cms.jq = jQuery;
cms.jq.ajaxSetup ({
    cache: false
});
cms.us = _;
cms.bb = Backbone;
alert(cms);
return cms;
});



