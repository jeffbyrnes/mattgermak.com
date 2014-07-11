<?php require("../../../../wp-blog-header.php"); ?>

/*
MyClass = Class.create();
MyClass.prototype = {
    initialize: function(a, b) {
        this.a = a;
        this.b = b;
		this.secondfunc();
    },
    secondfunc: function(){
		this.t = 'secondlife';
	}
}

var mc = new MyClass("foo", "bar");
alert(mc.t)
*/
	

Livesearch = Class.create();

Livesearch.prototype = {
	initialize: function(attachitem, target, remove, url, pars, button, loaditem, searchtext) {
		this.attachitem = attachitem;
		this.target = target;
		this.remove = remove;
		this.url = url;
		this.pars = pars;
		this.button = button;
		this.loaditem = loaditem;
		this.searchtext = searchtext;
		this.t = null;  // Init timeout variable

		// Style the searchform for livesearch
		$(button).style.display = 'none';
		$(attachitem).className = 'livesearch';
		$(attachitem).setAttribute('autocomplete','off');
		$(attachitem).setAttribute('value', this.searchtext);
		Event.observe(attachitem, 'focus', function() { if ($(attachitem).value == searchtext) $(attachitem).setAttribute('value', '') });
		Event.observe(attachitem, 'blur', function() { if ($(attachitem).value == '') $(attachitem).setAttribute('value', searchtext) });

		// Bind the keys to the input
		Event.observe(attachitem, 'keyup', this.readyLivesearch.bindAsEventListener(this));
	},

	readyLivesearch: function(event) {
		var code = event.keyCode;
		var currentLivesearch = this;
		if (code == Event.KEY_ESC || ((code == Event.KEY_DELETE || code == Event.KEY_BACKSPACE) && $F(this.attachitem) == '')) {
			this.resetLivesearch();
		} else if (code != Event.KEY_LEFT && code != Event.KEY_RIGHT && code != Event.KEY_DOWN && code != Event.KEY_UP && code != Event.KEY_RETURN) {
			if (this.t) { clearTimeout(this.t) };
	        this.t = setTimeout(this.doLivesearch.bind(this), 400);
		}
	},

	doLivesearch: function() {
		$(this.remove).style.display = 'none';

		new Ajax.Updater(
			this.target,
			this.url,
			{
				method: 'get',
				parameters: this.pars + $F(this.attachitem),
				onComplete: Effect.Fade(this.loaditem, {duration: .2}),
				onLoading: Effect.Appear(this.loaditem, {duration: .2})
		});
	},

	resetLivesearch: function() {
		$(this.attachitem).value = '';
		$(this.target).innerHTML = null;
		$(this.remove).style.display = null;
	}
}

Event.observe(window, "load", function() { new Livesearch('searchinput', 'dynamiccontent', 'primarycontent', '<?php bloginfo('template_url'); ?>/theloop.php', 'livesearch=1&s=', 'searchsubmit', 'rollload', 'Type & Wait to Search'); } , false);
