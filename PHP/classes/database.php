<?PHP
class DatabaseConnector
{
	protected $connection;

	public function __construct(string $host = 'localhost', int $port = 1433, string $db, string $user, string $pass, string $type, string $charset = 'utf8mb4', bool $trustCertificate = false)
	{
		try
		{
			$this->connection = new PDO("$type:server=$host;database=$db;TrustServerCertificate=$trustCertificate", $user, $pass);
			// $this->connection = new PDO("$type:host=$host;port=$port;charset=$charset;dbname=$db;TrustServerCertificate=$trustCertificate", $user, $pass);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			exit($e->getMessage());
		}

		return $this->connection;
	}

	public function executeStatement($query = '', $params = [])
	{
		try
		{
			$stmt = $this->connection->prepare($query);

			if ($stmt === false)
				throw new Exception('Unable to do prepared statement: ' . $query);

			$stmt->execute($params);
			return $stmt;
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}

	public function select($query = '', $params = [])
	{
		try
		{
			$stmt = $this->executeStatement($query, $params);
			return $stmt->fetchAll();
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());
		}
		return false;
	}

	public function update($query = '', $params = [])
	{
		try
		{
			$stmt = $this->executeStatement($query, $params);
			return $stmt->rowCount();
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());
		}
		return false;
	}

	public function listTables()
	{
		try
		{
			$stmt = $this->executeStatement('SELECT * FROM SYSOBJECTS WHERE xtype = \'U\'');
			return $stmt->fetchAll();
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());
		}
		return false;
	}
}
