<?PHP
function recursive_unset_key(array &$array, mixed $unwantedKey)
{
	unset($array[$unwantedKey]);
	foreach ($array as &$value)
	{
		if (is_array($value))
			recursive_unset_key($value, $unwantedKey);
	}
}

function recursive_unset_value(array &$array, mixed $unwantedValue)
{
	foreach ($array as $key => &$value)
	{
		if (is_array($value))
			recursive_unset_value($value, $unwantedValue);
		else if ($value === $unwantedValue)
			unset($array[$key]);
	}
}

//$arr => original array
//$set => array containing old keys as keys and new keys as values
function recursive_change_key(array $arr, array $set)
{
	if (is_array($arr) && is_array($set))
	{
		$newArr = array();
		foreach ($arr as $k => $v)
		{
			$key = array_key_exists($k, $set) ? $set[$k] : $k;
			$newArr[$key] = is_array($v) ? recursive_change_key($v, $set) : $v;
		}
		return $newArr;
	}
	return $arr;
}
