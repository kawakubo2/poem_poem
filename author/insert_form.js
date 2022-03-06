$(function() {
	$('#fm').submit(function(e) {
		let errors = [];
		let penname = $('#penname').val();
		if (penname === '') {
			errors.push('<li>ペンネームは必須入力です。</li>');
		}
		if (errors.length > 0) {
			$('#error_summary').html(errors.join(''));
			e.preventDefault();
		}
	});
});