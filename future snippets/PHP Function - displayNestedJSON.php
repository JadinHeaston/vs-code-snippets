function displayNestedJSON($givenArray, $previousKeys = NULL)
{
    if (is_array($givenArray)) //Verify given item is an array.
    {
        foreach($givenArray as $key=>$arrayItem) //Main array.
        {
            if (is_array($arrayItem))
            {
                if ($previousKeys === NULL)
                    displayNestedJSON($arrayItem, $key);
                else
                    displayNestedJSON($arrayItem, $previousKeys.' > '.$key);
            }
            else
                echo $previousKeys.' > '.$key." : ".$arrayItem.'<br>';
        }
    }
    else
    {
        echo '( ';
        var_dump($givenArray);
        echo ' ) is not an array.';
    }
}