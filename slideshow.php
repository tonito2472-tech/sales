<?php require 'modules/module-initialize.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php if($row[model_year]) echo $row[model_year]." "; echo "$row[make] $row[model]"; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel='stylesheet' href='styles/styles.css' media='all'/>
<link rel='stylesheet' href='styles/slideshow.css' media='all'/>
<script type='text/javascript'>

var photos  = new Array(<?php echo "'".implode("','", $image_set)."'"; ?>);
var start   = 0; // array index of first slide
var end     = <?php echo $row[images]-1; ?>; // array index of last slide
var current = start;
var doplay  = true; // do not play show automatically

// skip to first slide
function first() {
	current = 0;
	change();
}

// advance to next slide
function previous() {
	current -= 1;
	if(current < start) current = end; // skip to last slide
	change();
}

// go back to previous slide
function next() {
	current += 1;
	if(current > end) current = start; // skip to first slide
	change();
}

// skip to last slide
function last() {
	current = end;
	change();
}

// change slide according to value of current
function change() {
	document.photo.src = 'enlarge/' + photos[current];
}

// play automatic slideshow
function play() {
	if(doplay == true) {
		next();
		setTimeout(play, 2500); // call play() in 2.5 seconds
	}
}

// pause slideshow
function pause() {
	doplay = false;
}

</script>
</head>

<body>

<div id='container'>
	
	<?php require 'modules/module-slideshow.php'; ?>

</div>

</body>
</html>
