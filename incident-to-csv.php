#!/usr/bin/php
<?php

if (empty($argv[1])) die("The json file name or URL is missed\n");
$sJSONFilename = $argv[1];

$JSONData = file_get_contents($sJSONFilename);
$aDecodedJSONData = json_decode($JSONData, true);
$fOutputFileHandle = fopen(basename($sJSONFilename, '.txt').".csv", 'w');  

function FlattenIncident($aIncident) {

  $aFlattenIncident = array();
  foreach($aIncident as $key => $value) {
    if(is_array($value)) { 
      foreach($value as $subkey => $subvalue) {
	$aFlattenIncident[$key.".".$subkey] = $subvalue;
      }
    }
    else {
      $aFlattenIncident = array_merge($aFlattenIncident, array($key => $value));
    }
  }
  return $aFlattenIncident;
  
}

function FlattenIncidents($aIncidents) {
  
  $aFlattenIncidents = array();
  foreach ($aIncidents as $key => $value) {
    array_push($aFlattenIncidents, (array)FlattenIncident($value));
  }
  return $aFlattenIncidents;
  
}

$aFlattenJSONData = FlattenIncidents($aDecodedJSONData["incidents"]);

$firstLineKeys = false;
foreach ($aFlattenJSONData as $line) {
  if (empty($firstLineKeys)) {
    $firstLineKeys = array_keys($line);
    fputcsv($fOutputFileHandle, $firstLineKeys);
    $firstLineKeys = array_flip($firstLineKeys);
  }
  fputcsv($fOutputFileHandle, array_merge($firstLineKeys, $line));
}

fclose($fOutputFileHandle);

?>