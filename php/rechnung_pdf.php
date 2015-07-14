<?php


namespace Rechnungen {

  require './themed_pdf.php';

  define('EURO', utf8_encode(chr(128)));

  function number($betrag) {
    return number_format($betrag, 2, ',', '.') . ' ' . EURO;
  }

  class PDF extends \ThemedPDF\PDF {

    function faltmarken() {
      $this -> applyTheme('marks');
      $this -> pdf -> SetLineWidth(.3);
      $this -> pdf -> Line(0, 105, 7, 105);
      $this -> pdf -> Line(0, 105 + 43.5 + 43.5, 7, 105 + 43.5 + 43.5);
    }

    function init($settings) {
      $this -> settings = $settings;
      if(!array_key_exists('tablelayout', $this -> settings)) {
        $this -> settings['tablelayout'] = array(
          'datum' => array('Datum', 20, false),
          'menge' => array('Menge/Einheit', 75, true),
          'text'  => array('Leistung', 80, false),
          'summe' => array('Preis (EURO)', 200, true, ['number'])
        );
      } else {
        foreach($this -> settings['tablelayout'] AS $key => $layout) {
          if(!array_key_exists(0, $layout)) $layout[0] = $key;
          if(!array_key_exists(1, $layout)) throw new \Exception('Layout-Error!');
          if(!array_key_exists(2, $layout)) $layout[2] = false;
          if(!array_key_exists(3, $layout)) $layout[3] = $key;
          if(!is_array($layout[3])) {
            $layout[3] = array($layout[3]);
          }
          $this -> settings['tablelayout'][$key] = $layout;
        }
      }
      $this -> pdf -> fontpath = __DIR__ . '/fonts/';
      $this -> pdf -> AddFont('Ubuntu', '', 'Ubuntu-C.php');
      $this -> pdf -> AddFont('Ubuntu', 'B', 'Ubuntu-B.php');
      $this -> pdf -> AddFont('Ubuntu', 'BI', 'Ubuntu-BI.php');
      $this -> pdf -> AddFont('Ubuntu', 'R', 'Ubuntu-R.php');
      $this -> pdf -> AddFont('Ubuntu', 'RI', 'Ubuntu-RI.php');
      $this -> pdf -> AddFont('Ubuntu', 'L', 'Ubuntu-L.php');
      $this -> pdf -> AddFont('Ubuntu', 'LI', 'Ubuntu-LI.php');
      $this -> pdf -> AddPage();
    }

    function header() {
      $headertexts = array(
        'Softwareentwicklung'
      , 'Tel.: 040 - 202 3797 1'
      , 'Tobias Kopelke'
      , 'Mobil: 0160 - 811 0460'
      );

      $this -> applyTheme('header1');
      $this -> Text(20, 10, strtoupper($headertexts[0]));
      $this -> Text(200, 10, strtoupper($headertexts[1]), true);
      $this -> pdf -> Line(20, 15, 200, 15);

      $this -> applyTheme('header2');
      $this -> Text(20, 20, strtoupper($headertexts[2]));
      $this -> Text(200, 20, strtoupper($headertexts[3]), true);
      $this -> pdf -> Line(20, 25.5, 200, 25.5);

      $this -> applyTheme('space');
      $this -> pdf -> SetLineWidth(1);
      $this -> pdf -> Line(20, 34.5, 200, 34.5);
    }

    function footer() {
      $this -> applyTheme('space');
      $this -> pdf -> Line(20, 280, 200, 280);
      $this -> applyTheme('footer');
      $this -> Text(200, 282, 'Tobias Kopleke | Forbacher Strasse 9 | 22049 Hamburg | St.Nr.: 43/127/01796', true);
      $this -> Text(200, 286, 'Sparda Bank Hamburg eG | BIC GENODEF1S11 | IBAN DE47 2069 0500 0003 5042 12 ', true);
    }

    function adresse(array $adresse) {
      $this -> applyTheme('address');
      $this -> Text(30, 53, 'An:');
      foreach($adresse AS $index => $zeile) {
        $this -> Text(100, 53 + 10 * $index, $zeile, true);
      }
    }

    function titel($links, $rechts) {
      $this -> applyTheme('title');
      $this -> Text(25, 40, $links);
      $this -> Text(195, 40, $rechts, true);
      $this -> pdf -> Line(25, 44.4, 195, 44.4);
    }

    function drawCell($y, $value, $element) {
      if(array_key_exists(4, $element)) {
        $theme = $element[4];
        if(is_string($theme)) $this -> applyTheme($theme);
        else $this -> apply($theme);
      }

      // get layout element-data
      list($title, $pos, $orientation) = $element;
      // check if this is the title row
      if(is_null($value)) {
        $value = $title;
      } else {
        if(array_key_exists(3, $element)) {
          foreach ($element[3] as $mod) {
            switch ($mod) {
              case 'number': $value = number($value); break;
            }
          }
        }
      }

      $this -> Text($pos, $y, $value, $orientation);

      if(array_key_exists(5, $element)) {
        $theme = $element[5];
        if(is_string($theme)) $this -> applyTheme($theme);
        else $this -> apply($theme);
      }
    }

    function tableCell($y, $einheit) {
      foreach($this -> settings['tablelayout'] AS $key => $element) {
        #echo $key . " " . $einheit -> $key; echo "\n";
        $this -> drawCell($y, $einheit -> $key, $element);
      }
      $y += $this -> pdf -> FontSize + 1;
      $this -> pdf -> Line(20, $y, 200, $y);
    }

    function table($brutto, $steuer, $netto, $einheiten, $inklusive) {
      $this -> applyTheme('text');
      $this -> Text(20, 105, 'Für Ihren Auftrag bedanke ich mich und stelle folgende Leistungen in Rechnung:');

      $dy   = 7;
      $pad  = $dy * 9 / 2;
      $sy   = 105 + $pad;
      $ey   = $sy + $dy * count($einheiten) + $dy * 7 / 5;
      $y    = $ey + 2 * $dy + $pad;

      $this -> applyTheme('table');

      $this -> tableCell($sy - $dy, null);

      foreach($einheiten AS $index => $einheit) {
        $this -> tableCell($sy + $index * $dy, $einheit);
      }

      if(array_key_exists('tableFooter', $this -> settings)) {
        $this -> drawCell($sy + count($einheiten) * $dy, null, $this -> settings['tableFooter']);
      }

      $this -> applyTheme('table_extra');


      # Hier ändern wir das delta-y um mehr Zeilenabstand zu generieren um die Unterstriche zu ermöglichen
      $dy   = $dy * 6 / 5;
      $dy_  = $this -> pdf -> FontSize + 1;

      $this -> pdf -> Line(170, $ey + $dy_, 200, $ey + $dy_);
      $this -> pdf -> Line(170, $ey + $dy + $dy_, 200, $ey + $dy + $dy_);
      $this -> pdf -> Line(170, $ey + 2 * $dy + $dy_, 200, $ey + 2 * $dy + $dy_);

      $inkl = $inklusive ? 'davon' : 'inklusive';
      $this -> Text(165, $ey, 'Rechnungsbetrag (netto)', true);
      $this -> Text(165, $ey + $dy, $inkl . ' 19% Umsatzsteuer', true);
      $this -> Text(165, $ey + 2 * $dy, 'Rechnungsbetrag (brutto)', true);

      $this -> applyTheme('table');

      $this -> Text(200, $ey, number($netto), true);
      $this -> Text(200, $ey + $dy, number($steuer), true);
      $this -> Text(200, $ey + 2 * $dy, number($brutto), true);

      $this -> applyTheme('text');
      $this -> Text(20, $y, 'Bitte überweisen Sie den Rechnungsbetrag innerhalb von 10 Tagen auf das unten angegebene Konto.');
#      $this -> Text(20, $y + 8, 'Für die Leistung wurde eine Tagessatz von 700.50 EUR berechnet.');
    }
  }
}

