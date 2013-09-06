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

    function init() {
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
        'Softwareentwicklung - Webdesign'
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
      $this -> Text(200, 282, 'Forbacher Strasse 9 | 22049 Hamburg | St.Nr.: 54/353/12022', true);
      $this -> Text(200, 286, 'Sparda Bank Hamburg eG | Tobias Kopelke | Ktn.: 3504212 | BLZ.: 20690500', true);
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

    function tableCell($y, $einheit) {
      $this -> Text(20,  $y, $einheit -> datum);
      $this -> Text(75,  $y, $einheit -> menge, true);
      $this -> Text(80,  $y, $einheit -> text);
      $this -> Text(200, $y, $einheit -> preis, true);
      $y += $this -> pdf -> FontSize + 1;
      $this -> pdf -> Line(20, $y, 200, $y);
    }

    function table($einheiten, $inklusive) {
      $this -> applyTheme('text');
      $this -> Text(20, 105, 'Für Ihren Auftrag bedanke ich mich und stelle folgende Leistungen in Rechnung:');

      $dy   = 7;
      $pad  = $dy * 9 / 2;
      $sy   = 105 + $pad;
      $ey   = $sy + $dy * count($einheiten) + $dy * 7 / 5;
      $y    = $ey + 2 * $dy + $pad;

      $tableHeader = new \stdClass();
      $tableHeader -> datum = 'Datum';
      $tableHeader -> menge = 'Menge / Einheit';
      $tableHeader -> text  = 'Leistung';
      $tableHeader -> preis = 'Preis (EURO)';

      $this -> applyTheme('table');

      $this -> tableCell($sy - $dy, $tableHeader);

      $summe = 0;
      foreach($einheiten AS $index => $einheit) {
        $menge = $einheit -> menge;
        if(gettype($menge) === 'number') {
          $betrag = floor(($einheit -> menge * $einheit -> preis) * 100) / 100;
        } else {
          if(preg_match('/^(\d+):(\d+)$/', $menge, $matches)) {
            list($_, $h, $m) = $matches;
            $betrag = floor((($h + $m / 60) * $einheit -> preis) * 100) / 100;
          } else {
            $betrag = $einheit -> preis;
          }
        }
        $summe  += $betrag;
        $einheit -> preis = number($betrag);
        $this -> tableCell($sy + $index * $dy, $einheit);
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

      if($inklusive) {
        $this -> Text(200, $ey, number($summe / 1.19), true);
        $this -> Text(200, $ey + $dy, number($summe / 1.19 * .19), true);
        $this -> Text(200, $ey + 2 * $dy, number($summe), true);
      } else {
        $this -> Text(200, $ey, number($summe), true);
        $this -> Text(200, $ey + $dy, number($summe * .19), true);
        $this -> Text(200, $ey + 2 * $dy, number($summe * 1.19), true);
      }

      $this -> applyTheme('text');
      $this -> Text(20, $y, 'Bitte überweisen Sie den Rechnungsbetrag innerhalb von 10 Tagen auf das unten angegebene Konto.');
    }
  }
}

