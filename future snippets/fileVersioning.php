<?PHP

//Create version hashes based on last modified time.
$versionedFiles = array(
	'/path/to/file0/' => '',
	'/path/to/file1/' => '',
	'/path/to/file2/' => '',
);

foreach ($versionedFiles as $fileName => $hash)
{
	$versionedFiles[$fileName] = substr(md5(filemtime($fileName)), 0, 6);
}
