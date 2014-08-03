<?php

namespace {
  function generatePDF(FPDF $pdf, $data) {
    $rechnung = new Rechnungen\PDF($pdf);
    $rechnung -> init($data['settings']);

    global $theme;

    $text = $data['text'];
    if(!$text) $text = array();

    $title = $data['rechnungsnummer'];
    $datum = $data['datum'];
    if(array_key_exists('title', $text)) $title = $text['title'];
    if(array_key_exists('datum', $text)) $datum = $text['datum'];

    $rechnung -> setTheme($theme);
    $rechnung -> header();
    $rechnung -> titel($title, $datum);
    $rechnung -> adresse($data['adresse']);
    $rechnung -> table($data['brutto'], $data['steuer'], $data['netto'], $data['einheiten'], $data['inklusive']);
    $rechnung -> faltmarken();
    $rechnung -> footer();
    return $rechnung -> pdf;
  }
}
