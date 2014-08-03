$('#eng_article_btn').on('click', function(){
	if(document.getElementById('check_post').value != 1)
		document.getElementById('check_post').value = 1;
	$lang = "English";
	$.post("ajax/ajax.php", {lang : $lang}, function(data){
		$('#para').text(data);
		changeFontRandomly();
	});
});
$('#hindi_article_btn').on('click', function(){
	if(document.getElementById('check_post').value != 1)
		document.getElementById('check_post').value = 1;
	$lang = "Hindi";
	$.post("ajax/ajax.php", {lang : $lang}, function(data){
		$('#para').text(data);
		changeFontRandomly();
	});
});