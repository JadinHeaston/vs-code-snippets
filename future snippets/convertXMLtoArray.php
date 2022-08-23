//Parses the XML into a PHP array.
function convertXMLtoArray($xml)
{
    $outputArray = [];

    $tempJSON = json_encode($xml);
    $outputArray = json_decode($tempJSON, TRUE);

    return $outputArray;
}