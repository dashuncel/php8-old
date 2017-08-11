<?php
  include_once __DIR__.DIRECTORY_SEPARATOR.'Gd.php';
  session_start();
  $text = $_SESSION['text'];
  $img  = new Gd(500, 200, $text);
  $img->generate();
 
