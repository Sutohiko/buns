$('.checkbox').on('click', function () {
	pf = [];

	$('input[name^=\'prostofilter\']:checked').each(function(element) {
		pf.push(this.value);
	});

	if (document.getElementById('ex2') != null) {
		pf.push('minprice[' + slider.getValue()[0] + ']');
		pf.push('maxprice[' + slider.getValue()[1] + ']');
	}

	$.ajax({
		url: 'index.php?route=extension/module/prostofilter/Total',
		type: 'POST',
		data: {category_id:<?php echo $category_id; ?>,filter:pf.join(',')},
		dataType: 'JSON',

		success: function(json) { //Данные отправлены успешно
			console.log('success');

			console.log('count: ' + json['success']);

			$('#button-prostofilter').text('Показать ' + json['success'] + ' товаров');

		},
		error: function(json) {
			console.log('error');
		}

	});
});