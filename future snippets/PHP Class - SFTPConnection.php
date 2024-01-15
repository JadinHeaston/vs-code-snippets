<?php

namespace jbh;

class SFTPConnection
{
	private $connection;
	private $sftp;

	public function __construct(string $host, int $port = 22)
	{
		$this->connection = @ssh2_connect($host, $port);
		if (!$this->connection)
			throw new \Exception("Could not connect to $host on port $port.");
	}

	public function login(string $username, string $password)
	{
		if (!@ssh2_auth_password($this->connection, $username, $password))
			throw new \Exception("Could not authenticate with username $username " .
				"and password $password.");

		$this->sftp = @ssh2_sftp($this->connection);
		if (!$this->sftp)
			throw new \Exception('Could not initialize SFTP subsystem.');
	}

	public function uploadFile(string $local_file, string $remote_file)
	{
		$stream = @fopen("ssh2.sftp://" . intval($this->sftp) . "/SFTP/inbound/$remote_file", 'w');

		if (!$stream)
			throw new \Exception("Could not open file: $remote_file");

		$data_to_send = @file_get_contents($local_file);
		if ($data_to_send === false)
			throw new \Exception("Could not open local file: $local_file.");

		if (@fwrite($stream, $data_to_send) === false)
			throw new \Exception("Could not send data from file: $local_file.");

		@fclose($stream);
	}


	function scanFilesystem(string $remote_file, bool $recursive = true)
	{
		$dir = "ssh2.sftp://$this->sftp/$remote_file";
		$tempArray = array();
		$handle = opendir($dir);

		//List all the files
		while (false !== ($file = readdir($handle)))
		{
			if (substr($file, 0, 1) === '.')
				continue;

			if ($recursive && is_dir($file))
				$tempArray[$file] = $this->scanFilesystem("$dir/$file");
			else
				$tempArray[] = $file;
		}
		closedir($handle);
		return $tempArray;
	}

	public function receiveFile(string $remote_file, string $local_file)
	{
		$stream = @fopen("ssh2.sftp://$this->sftp/$remote_file", 'r');
		if (!$stream)
			throw new \Exception("Could not open file: $remote_file");
		$contents = fread($stream, filesize("ssh2.sftp://$this->sftp/$remote_file"));
		file_put_contents($local_file, $contents);
		@fclose($stream);
	}

	public function deleteFile(string $remote_file)
	{
		unlink("ssh2.sftp://$this->sftp/$remote_file");
	}
}