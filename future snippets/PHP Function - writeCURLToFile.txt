function writeCURLToFile($url, $file)
{
    $curl = curl_init(); //Initiallize curl. Create a session.
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    $curlResults = curl_exec($curl); //Execute 
    curl_close($curl); //Close curl session.

    if (is_resource($file)) //if the provided object is a legit file handle.
        fwrite($file, $curlResults);
    else //Otherwise, create a file handle and write to it.
    {
        $CURLFile = fopen($file, "w"); //Creating file handle.
        fwrite($CURLFile, $curlResults); //Writing to file.
        fclose($CURLFile); //Closing file.
    }
}