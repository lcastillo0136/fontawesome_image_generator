<?php
  $t = $_GET["t"]; // Name of the class without "fa": "file-text-o"
  $s = $_GET["size"];  // Font Size 1-7
  $c = $_GET["color"]; // Font Color, Hex format without "#": "fff" or "ffffff"
  $w = $_GET["width"]; // Output Image Width
  $h = $_GET["height"]; // Output Image Height

  $c = str_replace("#", "", $c);
  $w = str_replace(array("%", "px" , "pt"), "", $w);
  $h = str_replace(array("%", "px" , "pt"), "", $h);

  $searchClass = $t.":before";
  $lines = file("rute/for/font/awesome.css");

  foreach($lines as $line) {
    if (strstr($line, $searchClass) !== false) {
      $insideSearchedTag = true;
    }
    else if (strstr($line, "}") !== false && $insideSearchedTag) {
      $insideSearchedTag = false;
      break;
    }
    else if ($insideSearchedTag) {
      if (strstr($line, "content:") !== false) {
        $n .= $line;
      }
    }
  }

  $faIcon = trim(str_replace(array("content:", " ", '"', ';', '\\', '\n'), "", $n));

  if ($s == "" || !isset($s) || $s > 7) {
    $s = 7;
  }

  if ($c == "" || !isset($c)) {
    $c = array(0x0,0x0,0x0);
  } else {
    if (strlen($c) === 3) {
      $c = array_map('shortHex',str_split($c,1));
    } else {
      $c = array_map('hexdec',str_split($c,2));
    }
  }

  echo makeFontAwesomeImage("&#x".$faIcon.";", "css/font-awesome/fonts/fontawesome-webfont.ttf", $s, $c, $w, $h);


  function shortHex($ff) {
    return hexdec($ff.$ff);
  }

  function makeFontAwesomeImage($text, $font="CENTURY.TTF", $fsize=7, $color=array(0x0,0x0,0x0), $width, $height) {
    $outputFSizes = array('16', '24', '32', '48', '64', '96', '128');
    $outputISizes = array('20', '32', '42', '63', '85', '130', '174');
    $X = ($outputISizes[$fsize-1]*5/100);
    $Y = $outputISizes[$fsize-1]-($outputISizes[$fsize-1]*15/100);

    if (isset($width) && $width > 0) {
      $X = ($width*5/100);
    } else {
      $width = $outputISizes[$fsize-1];
    }

    if (isset($height) && $height > 0) {
      $Y = $height-($height*15/100);
    } else {
      $height = $outputISizes[$fsize-1];
    }

    $im = @imagecreate($width, $height);
        
    $background_color = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
    imagecolortransparent($im, $background_color);
    $text_color = imagecolorallocate($im, $color[0], $color[1], $color[2]);
    imagettftext($im, $outputFSizes[$fsize-1], 0, $X, $Y, $text_color, $font, $text);
    
    header("Content-type: image/png");                
    return imagepng($im);
  }
?>
