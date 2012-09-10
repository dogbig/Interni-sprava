/* Czech initialisation for the jQuery UI date picker plugin. */
/* Written by Tomas Muller (tomas@tomas-muller.net). Fixed by Michal Charvát. */
jQuery(function($){
	$.datepicker.regional['cs'] = {
		closeText: 'Zav\u0159ít',
		prevText: '&#x3c;D\u0159íve',
		nextText: 'Pozd\u011bji&#x3e;',
		currentText: 'Nyní',
		monthNames: ['leden','únor','b\u0159ezen','duben','kv\u011bten','\u010derven',
        '\u010dervenec','srpen','zá\u0159í','\u0159íjen','listopad','prosinec'],
		monthNamesShort: ['led','úno','b\u0159e','dub','kv\u011b','\u010der',
		'\u010dvc','srp','zá\u0159','\u0159íj','lis','pro'],
		dayNames: ['ned\u011ble', 'pond\u011blí', 'úterý', 'st\u0159eda', '\u010dtvrtek', 'pátek', 'sobota'],
		dayNamesShort: ['ne', 'po', 'út', 'st', '\u010dt', 'pá', 'so'],
		dayNamesMin: ['ne','po','út','st','\u010dt','pá','so'],
		weekHeader: 'Týd',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['cs']);
});
