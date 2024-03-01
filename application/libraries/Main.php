<?php

use PhpParser\Builder\Class_;

 defined('BASEPATH') OR exit('No direct script access allowed');

Class Main{

    function slug($text)
    {

        $text = trim($text);
        $find = array(' ', '/', '&', '\\', '\'', ',', '(', ')', '?', '!', ':');
        $replace = array('-', '-', 'and', '-', '-', '-', '', '', '', '', '');

        $slug = str_replace($find, $replace, strtolower($text));

        return $slug;
    }
}