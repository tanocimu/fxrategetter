<?php

/*
   -----------------------------------------------------
   �ȈՃA�N�Z�X�J�E���^�[
   Ver. 1.0.2
   update 2017.8.9
   Copyright (C) WEB-JOZU  URL:http://www.web-jozu.com/
   -----------------------------------------------------
*/


session_start();

//GET��pos�𐮐��ɕϊ�
$pos = intval($_GET['pos']);


//�t�@�C����
$fileName = "count.php";

//�t�@�C���ǂݍ���
$file = fopen($fileName, "r+") or die("�t�@�C���̃I�[�v���Ɏ��s���܂���");
flock($file, LOCK_EX);
$fileVal = fgets($file, 10000);

//�J�E���g�𑝂₷
$countVal = explode("=", $fileVal);
$num = intval($countVal[1]);

if($pos == "0" && $_SESSION['first'] != "on"){
	$num++;
}else if($pos == "1" && $_SESSION['first'] != "on"){
	$_SESSION['first'] = "on";
}

$writeVal = "count=" . $num . ";";


//�t�@�C����������
if($pos == "0" && $_SESSION['first'] != "on"){
	rewind($file);
	fwrite($file, $writeVal);
}
flock($file, LOCK_UN);
fclose($file);


if($pos != 0){

	if(strlen($num) < $pos){
		$imgNum = 0;
	}else{
		$imgNum = substr($num, -$pos, 1);
	}

	//�摜�\��
	$fileName = "img/" . $imgNum . ".gif";

	header("Content-type: image/gif");
	readfile($fileName);
}

?>