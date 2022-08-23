function is_connectable(string $host, int $port=80, int $timeout=3)
{
     if($fp = @fsockopen($host, $port, $errno, $errstr, $timeout))
		return fclose($fp) || true;

     return false;
}