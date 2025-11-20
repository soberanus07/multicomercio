<?php

$jsonString =file_get_contents("02022014.json");;
$jsonDecoded = json_decode($jsonString, true);
$csvHeader=array();
$csvData=array();
$csvFileName = 'file.csv';
$fp = fopen($csvFileName, 'w');
$counter=0;
foreach($jsonDecoded["list"] as $key => $value)
{
    jsontocsv($value);
    if($counter==0)
    {
        fputcsv($fp, $csvHeader);
        $counter++;
    }
    fputcsv($fp, $csvData);
    $csvData=array();
}
fclose($fp);

function jsontocsv($data)
{
    global $csvData,$csvHeader;
    foreach($data as $key => $value)
    {
        if(!is_array($value))
        {
            $csvData[]=$value;
            $csvHeader[]=$key;
        }
        else 
        {
            jsontocsv($value);
        }
    }
}