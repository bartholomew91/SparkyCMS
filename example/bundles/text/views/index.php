<?php
if (is_null($text))
{
	echo "No text, why don't you add some?";
}

if ( ! is_null($text))
{
	echo $text->content;
}