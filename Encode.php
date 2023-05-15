<?php
function e($str, $charset = 'UTF-8') {
	if ($str === NULL || $str === '') return TRUE;
	return htmlspecialchars($str, ENT_QUOTES, $charset);
}
