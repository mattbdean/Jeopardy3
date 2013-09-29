/*global jQuery, $*/
/*jslint browser: true, white: true, devel:true */

$(function() {
	"use strict";
	$('.gameboard').click(function(){
		$('#darkness').fadeTo(200, 1);
		$('#popupQuestion').fadeTo(200, 1);
	});
});