<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

function bCrypt($pass,$cost){
      $chars='./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
      $salt=sprintf('$2a$%02d$',$cost);
      mt_srand();
      for($i=0;$i<22;$i++) $salt.=$chars[mt_rand(0,63)];
    return crypt($pass,$salt);
}
function generate_username($string_name, $rand_no = 200){
    $username_parts = array_filter(explode(" ", strtolower($string_name))); //explode and lowercase name
    $username_parts = array_slice($username_parts, 0, 2); //return only first two arry part

    $part1 = (!empty($username_parts[0]))?substr($username_parts[0], 0,8):""; //cut first name to 8 letters
    $part2 = (!empty($username_parts[1]))?substr($username_parts[1], 0,5):""; //cut second name to 5 letters
    $part3 = ($rand_no)?rand(0, $rand_no):"";
    
    $username = $part1. str_shuffle($part2). $part3; //str_shuffle to randomly shuffle all characters 
    return $username;
}
function intCodeRandom($length = 8)
{
  $intMin = (10 ** $length) / 10; // 100...
  $intMax = (10 ** $length) - 1;  // 999...
  $codeRandom = mt_rand($intMin, $intMax);
  return $codeRandom;
}

function slugify($string) {
	$string = utf8_encode($string);
	$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);   
	$string = preg_replace('/[^a-z0-9- ]/i', '', $string);
	$string = str_replace(' ', '-', $string);
	$string = trim($string, '-');
	$string = strtolower($string);
	if (empty($string)) {
		return 'n-a';
	}
	return $string;
}

function clearformat($nilai){
	return str_replace(',', '',$nilai);
}
function rupiah($angka)
{
    if(!empty($angka)){
        $hasil_rupiah = number_format($angka,0,",",".");
        return $hasil_rupiah;
    }else{
        return 0;
    }
}
function rupiah_koma($angka)
{
    if(!empty($angka)){
        $hasil_rupiah = number_format($angka,0,".",",");
        return $hasil_rupiah;
    }else{
        return 0;
    }
}

function tgl_indo($tanggal){
	$bulan = array (
		1 =>   'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);
	$pecahkan = explode('-', $tanggal);
	
	// variabel pecahkan 0 = tanggal
	// variabel pecahkan 1 = bulan
	// variabel pecahkan 2 = tahun
 
	return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}


 function trim_special_char($text)
    {
        $str = str_replace("(", '_:', $text);
        $str = str_replace(")", ':_', $str);
        $str = str_replace("/", '_slash', $str);
        $str = str_replace("+", '_plus', $str);
        $str = str_replace("&", '_and', $str);
        $str = str_replace("'", '_ss', $str);
        $str = str_replace("x", '_X', $str);
        $str = str_replace('"', '_cot', $str);
        $str = str_replace('!', '_cit', $str);

        return $str;
    }
	 function set_special_char($text)
    {
        $str = str_replace('_:',  "(", $text);
        $str = str_replace(':_', ")", $str);
        $str = str_replace('_slash', "/", $str);
        $str = str_replace('_plus', "+", $str);
        $str = str_replace('_and', "&", $str);
        $str = str_replace('_ss', "'", $str);
        $str = str_replace('_X', "x", $str);
        $str = str_replace('_cot', '"', $str);
        $str = str_replace('_cit', '!', $str);

        return $str;
    }


?>
