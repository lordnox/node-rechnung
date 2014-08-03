<?php

namespace ThemedPDF {

  function HexToRGB($_hex) {
    $hex = substr($_hex, 1);
    $color = array();

    if(strlen($hex) == 3) {
      $color['r'] = hexdec(substr($hex, 0, 1));
      $color['g'] = hexdec(substr($hex, 1, 1));
      $color['b'] = hexdec(substr($hex, 2, 1));
    }

    if(strlen($hex) == 6) {
      $color['r'] = hexdec(substr($hex, 0, 2));
      $color['g'] = hexdec(substr($hex, 2, 2));
      $color['b'] = hexdec(substr($hex, 4, 2));
    }

    return $color;
  }

  function RGBToHex($r, $g, $b) {
    //String padding bug found and the solution put forth by Pete Williams (http://snipplr.com/users/PeteW)
    $hex = "#";
    $hex.= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
    $hex.= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
    $hex.= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);

    return $hex;
  }


  class PDF {
    public $pdf = null;
    public $theme = array();

    function setTheme($theme) {
      $this -> theme = $theme;
      if(array_key_exists('fonts', $this -> theme)) {
        $path   = $this -> pdf -> fontpath;
        $fonts  = $this -> theme['fonts'];
        $this -> pdf -> fontpath = __DIR__ . $fonts['path'];

        foreach($fonts['add'] as $font) {
          $this -> pdf -> AddFont($font[0], $font[1], $font[2]);
        }

        $this -> pdf -> fontpath = $path;
      }
    }

    function applyTheme($code) {
      $this -> apply($this -> theme[$code]);
    }

    function apply($theme) {
      foreach($theme AS $fn => $arg) {
        $this -> {$fn}($arg);
      }
    }

    protected function decodeColor($code) { return HexToRGB($code); }

    protected function drawcolor($hex) {
      $colors = $this -> decodeColor($hex);
      $this -> pdf -> SetDrawColor($colors['r'], $colors['g'], $colors['b']);
    }

    protected function fillcolor($hex) {
      $colors = $this -> decodeColor($hex);
      $this -> pdf -> SetFillColor($colors['r'], $colors['g'], $colors['b']);
    }

    protected function textcolor($hex) {
      $colors = $this -> decodeColor($hex);
      $this -> pdf -> SetTextColor($colors['r'], $colors['g'], $colors['b']);
    }

    protected function color($hex) {
        $colors = $this -> decodeColor($hex);
        $this -> pdf -> SetDrawColor($colors['r'], $colors['g'], $colors['b']);
        $this -> pdf -> SetFillColor($colors['r'], $colors['g'], $colors['b']);
        $this -> pdf -> SetTextColor($colors['r'], $colors['g'], $colors['b']);
    }

    protected function lineWidth($w)    { $this -> pdf -> SetLineWidth($w); }
    protected function charSpaceing($w) { $this -> pdf -> CharSpacing = $w; }
    protected function font($f)         { $this -> pdf -> SetFont($f[0], $f[1], $f[2]); }

    protected function picture($x, $y, $w, $h, $src) {
      $this -> pdf-> Image($src, $x, $y, $w, $h);
    }

    protected function Text($x, $y, $_str, $right = false) {
      $str = utf8_decode($_str);
      if($right) {
        $x -= $this -> pdf -> GetStringWidth($str);
      }
      $y += $this -> pdf -> FontSize;
      $this -> pdf -> Text($x, $y, $str);
    }

    function __construct(\FPDF $pdf) {
      $this -> pdf = $pdf;
    }
  }
}

