<?php

// load the stream data from rechnung.coffee
$stdin = file_get_contents("php://stdin");

// create a rekursive convertion function
function obj2array($obj) {
  $result = (array) $obj;
  foreach($result AS $key => $value)
    if(gettype($value) === 'object')
      $result[$key] = obj2array($value);
  return $result;
}

// load the stream as a json string and convert all objects to assoc-arrays
$options = obj2array(json_decode($stdin));

// set the settings
$settings = array();
// try to read the settings from the options
if(array_key_exists('settings', $options)) {
  $settings = $options['settings'];
}
// throw if there is nothing to do
if(!array_key_exists('data', $options))
  throw new Exception("Rechnung.pdf.php needs data to work with");
// load the data
$data = $options['data'];

// @TODO Theme definition must be checked here
global $theme;
$theme = $settings['theme'];

chdir(__DIR__);

// load the fpdf lib
require './vendor/fpdf/fpdf.php';
require './rechnung_pdf.php';
require './rechnung.php';

global $pdf;
$pdf = new FPDF('P', 'mm', 'A4');

$rechnung = generatePDF($pdf, $options['data']);

$rechnung -> output();

#echo "Hello World!";#