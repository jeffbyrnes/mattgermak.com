function commentAdded(request) {
	if ($('errors')) { Element.hide('errors'); }
	new Effect.Appear($('commentlist').lastChild);
	$('comment').value = '';
	$('comment').disabled = true;
	$('submit').disabled = true;
	if ($('leavecomment')) { Element.remove('leavecomment'); }
	$('comments').innerHTML = parseInt($('comments').innerHTML) + 1;
	setTimeout('commentOpen();',15000);
}

function commentOpen() {
	$('comment').disabled = false;
	$('submit').disabled = false;
}

function failure(request) {
	Element.show('errors');
	$('errors').innerHTML = request.responseText;
	new Effect.Highlight('errors',{queue:'end'});
	if ($('leavecomment')) { Element.show('leavecomment'); }
}

function loading() {
	if ($('leavecomment')) { Element.hide('leavecomment'); }
	$('submit').disabled = true;
	$('comment').disabled = true;  
	Element.show('loading');
}

function complete(request) {
	Element.hide('loading');
	Element.show('commentform');
	commentOpen();

	if (request.status == 200) {commentAdded()}
	else {failure(request)};
}
