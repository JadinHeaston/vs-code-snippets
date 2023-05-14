<?PHP
//Create version hashes based on last modified time.
$versionedFiles = array(
	'/path/to/file/' => '',
	'/SecTrack2/Resources/css/style.css' => '',
	'/SecTrack2/Resources/css/queries.css' => '',
	'/SecTrack2/Admin/js/scripts.js' => '',
);

foreach($versionedFiles as $fileName => $hash)
{
	$versionedFiles[$fileName] = substr(md5(filemtime(str_replace('\\', '/', substr(realpath(__DIR__), 0, strpos(realpath(__DIR__), '{ROOT}') - 1)) . $fileName)), 0, 6);
}