<?php

namespace {
  function generatePDF(FPDF $pdf, $data) {
    $rechnung = new Rechnungen\PDF($pdf);
    $rechnung -> init();

    global $theme;

    $rechnung -> setTheme($theme);
    $rechnung -> header();
    $rechnung -> titel($data['rechnungsnummer'], $data['datum']);
    $rechnung -> adresse($data['adresse']);
    $rechnung -> table($data['einheiten'], $data['inklusive']);
    $rechnung -> faltmarken();
    $rechnung -> footer();
    return $rechnung -> pdf;
  }
}
