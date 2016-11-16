<?php
function french_replacements($text)
{
	$pairs = array(
		'/é/' => 'e',
		'/É/' => 'E',
		'/è/' => 'e',
		'/È/' => 'E',
		'/á/' => 'a',
		'/Á/' => 'A',
		'/à/' => 'a',
		'/À/' => 'A',
		'/â/' => 'a',
		'/Â/' => 'A',
		'/ô/' => 'o',
		'/Ô/' => 'O',
		'/î/' => 'i',
		'/Î/' => 'I',
		'/û/' => 'u',
		'/Û/' => 'U',
		'/ç/' => 'c',
		'/Ç/' => 'C',
	);
	
	$text = preg_replace(array_keys($pairs), array_values($pairs), $text);
	
	return $text;
}
?>