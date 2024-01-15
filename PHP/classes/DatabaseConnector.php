<?PHP

namespace jbh;

class DatabaseConnector
{
	protected $connection;
	protected $type;

	private $queries = array(
		'listTables' => array(
			'mysql' => 'SHOW FULL tables',
			'sqlite' => 'SELECT * FROM sqlite_schema WHERE type =\'table\' AND name NOT LIKE \'sqlite_%\'',
			'sqlsrv' => 'SELECT DISTINCT TABLE_NAME FROM information_schema.tables'
		),
		'getTableInformation' => array(
			'mysql' => 'DESCRIBE ?',
			'sqlite' => 'PRAGMA table_info(?)',
			'sqlsrv' => 'SELECT * FROM information_schema.columns WHERE TABLE_NAME = ? order by ORDINAL_POSITION'
		),
		'getTableIndexes' => array(
			'mysql' => 'SHOW INDEX FROM ?',
			'sqlite' => 'SELECT * FROM sqlite_master WHERE type = \'index\' AND tbl_name = ?',
			'sqlsrv' => 'SELECT * FROM sys.indexes WHERE object_id = (SELECT object_id FROM sys.objects WHERE name = ?)'
		),
		'getTableCreation' => array(
			'mysql' => 'SHOW CREATE TABLE ?',
			'sqlite' => 'SELECT sql FROM sqlite_schema WHERE name = ?',
			'sqlsrv' => false //Not available without a stored procedure.
		),
		'createTable' => array(
			'mysql' => 'CREATE TABLE IF NOT EXISTS ? ()',
			'sqlite' => 'CREATE TABLE IF NOT EXISTS ? (column_name datatype, column_name datatype);',
			'sqlsrv' => ''
		)
	);

	public function __construct(string $type, string $hostPath, int $port = null, string $db = '', string $user = '', string $pass = '', string $charset = 'utf8mb4', bool|null $trustCertificate = null)
	{
		$this->type = strtolower(trim($type));
		try
		{
			//Creating DSN string.
			$dsn = $this->type;
			if ($this->type === 'mysql')
				$dsn .= ':host=';
			elseif ($this->type === 'sqlite')
				$dsn .= ':';
			elseif ($this->type === 'sqlsrv')
				$dsn .= ':Server=';

			$dsn .= $hostPath;

			if ($this->type === 'mysql')
				$dsn .= ';port=' . strval($port);

			if ($this->type === 'mysql')
				$dsn .= ';dbname=';
			elseif ($this->type === 'sqlsrv')
				$dsn .= ';Database=';

			$dsn .= $db;

			if ($this->type === 'mysql')
				$dsn .= ';charset=' . $charset;
			if ($this->type === 'sqlsrv' && $trustCertificate !== null)
				$dsn .= ';TrustServerCertificate=' . strval(intval($trustCertificate));

			//Attempting connection.
			$this->connection = new \PDO($dsn, $user, $pass);
			$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
			$this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
			$this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		}
		catch (\PDOException $e)
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
				throw new \Exception('Unable to do prepared statement: ' . $query);

			$stmt->execute($params);
			return $stmt;
		}
		catch (\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}/**
 * Utility function to build HTML table from an array.
 * 
 * Reference: http://www.terrawebdesign.com/multidimensional.php
 */

    function htmlShowArray($array) {
        echo "<table cellspacing='0' border='1'>\n";
        show_array($array, 1, 0);
        echo "</table>\n";
    }


    function show_array($array, $level, $sub) {
        if (is_array($array) == 1) {          // check if input is an array
            foreach($array as $key_val => $value) {
                $offset = '';
                if (is_array($value) == 1){   // array is multidimensional
                    echo '<tr>';
                    $offset = do_offset($level);
                    echo $offset . '<td>' . $key_val . '</td>';
                    show_array($value, $level + 1, 1);
                } else { // (sub)array is not multidim
                    if ($sub != 1){ // first entry for subarray
                        echo '<tr nosub>';
                        $offset = do_offset($level);
                    }
                    $sub = 0;
                    echo $offset ."<td width='180px'>" . $value . '</td>';
                    echo "</tr>\n";
                }
            } //foreach $array
        } else { 
            // argument $array is not an array
            return;
        }
    }

    function do_offset($level){
        $offset = ''; // offset for subarry 
        for ($i=1; $i<$level;$i++) {
            $offset = $offset . '<td></td>';
        }
        return $offset;
    }

	public function select($query = '', $params = [])
	{
		try
		{
			$stmt = $this->executeStatement($query, $params);
			return $stmt->fetchAll();
		}
		catch (\Exception $e)
		{
			throw new \Exception($e->getMessage());
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
		catch (\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
		return false;
	}

	public function getLastInsertID(): string
	{
		return $this->connection->lastInsertId();
	}

	public function listTables($includeViews = true)
	{
		$query = $this->queries[__FUNCTION__][$this->type];
		if ($query === false)
			return false;

		if ($includeViews === false && $this->type === 'mysql')
			$query .= ' WHERE Table_Type = \'BASE TABLE\'';
		elseif ($includeViews === false && $this->type === 'sqlsrv')
			$query .= ' WHERE TABLE_TYPE = \'BASE TABLE\'';

		try
		{
			$stmt = $this->executeStatement($query);
			return $stmt->fetchAll();
		}
		catch (\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
		return false;
	}

	public function getTableInformation(string $table)
	{
		$query = $this->queries[__FUNCTION__][$this->type];
		if ($query === false)
			return false;

		elseif ($this->type === 'sqlite')
			$query = 'PRAGMA table_info(?)';
		elseif ($this->type === 'sqlsrv')
			$query = 'SELECT * FROM information_schema.columns WHERE TABLE_NAME = ? order by ORDINAL_POSITION';
		try
		{
			$stmt = $this->executeStatement($query, array($table));
			return $stmt->fetchAll();
		}
		catch (\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
		return false;
	}

	public function getTableIndexes(string $table)
	{
		$query = $this->queries[__FUNCTION__][$this->type];
		if ($query === false)
			return false;

		try
		{
			$stmt = $this->executeStatement($query, array($table));
			return $stmt->fetchAll();
		}
		catch (\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
		return false;
	}

	public function getTableCreation(string $table)
	{
		$query = $this->queries[__FUNCTION__][$this->type];
		if ($query === false)
			return false;

		try
		{
			$stmt = $this->executeStatement($query, array($table));
			return $stmt->fetchAll();
		}
		catch (\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
		return false;
	}

	//$columns is expected to follow the structure below:
	// [
	// 	0 => array(
	// 		'name' => '',
	// 		'type' => '',
	// 		'index' => false,
	// 		'primary' => false,
	// 		'null' => false,
	// 		'default' => '', //Any type.
	// 		'foreign_key' => array()
	// 	),
	// ]
	public function createTable(string $tableName, array $columns)
	{
		$query = $this->queries[__FUNCTION__][$this->type];
		if ($query === false)
			return false;

		try
		{
			$stmt = $this->executeStatement($query, array($tableName,));
			return $stmt->fetchAll();
		}
		catch (\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}

		return false;
	}
}

class Table
{
	public TableRows $rows;

	public function __construct(TableColumns $columns)
	{
		$this->rows = new TableRows($columns);
	}

	/**
	 * Returns array of rows
	 *
	 * @return Array<row>
	 */
	public function getRows()
	{
		return $this->rows->getRows();
	}

	public function importData(array $data)
	{
		if ($data === false)
			return false;
		foreach ($data as $row)
		{
			$this->rows->addRow(new Row($row));
		}

		return true;
	}

	public function listColumns(bool $fullyQualifiedName = false)
	{
		return $this->rows->listColumns($fullyQualifiedName);
	}

	public function getColumns()
	{
		return $this->rows->getColumns();
	}

	/**
	 * Returns HTML of the inputs.
	 *
	 * @return string
	 */
	public function displayInputs()
	{
		$output = '';
		foreach ($this->getRows() as $row)
		{
			foreach ($row->values as $key => $value)
			{
				$output .=  '<label>' . ucwords(str_replace('_', ' ', $key)) . '<input type="string" value="' .  $value . '">' . '</label>';
			}
		}

		return $output;
	}
}


/**
 * Columns pull the most amount of work.
 */
class TableColumns
{
	public array $columns = array();

	public function __construct(Column ...$columns)
	{
		foreach ($columns as $column)
		{
			$this->columns[$column->name] = $column;
		}
	}

	public function addColumn(Column $column)
	{
		$this->columns[$column->name] = $column;
		return true;
	}

	public function listColumns(bool $fullyQualifiedName = false)
	{
		$columnsNames = array();

		foreach ($this->getColumns() as $column)
		{
			$columnsNames[] = $column->getFullColumnName($fullyQualifiedName);
		}

		return $columnsNames;
	}

	/**
	 * Returns array of columns
	 *
	 * @return Array<Column>
	 */
	public function getColumns()
	{
		return $this->columns;
	}

	public function importData(array $data)
	{
		foreach ($data as $row)
		{
			foreach ($this->getColumns() as $columns)
			{
				$this->columns[$columns->name]->addValue($row, $columns->name);
			}
		}

		return true;
	}
}

/**
 * Rows store the actual data. Each row is made up of X number if columns 
 */
class TableRows
{
	public TableColumns $columns;
	public array $rows = array();

	public function __construct(TableColumns $columns)
	{
		$this->initializateColumns($columns);
	}

	public function addRow(row $row)
	{
		$this->rows[] = $row;
	}

	/**
	 * Returns array of rows
	 *
	 * @return Array<row>
	 */
	public function getRows()
	{
		return $this->rows;
	}

	private function initializateColumns(TableColumns $columns)
	{
		$this->columns = $columns;
	}

	public function listColumns(bool $fullyQualifiedName = false)
	{
		return $this->columns->listColumns($fullyQualifiedName);
	}
	
	public function getColumns()
	{
		return $this->columns->getColumns();
	}
}

class Column
{
	public string $name;
	/**
	 * Valid types: bool | email | int | json | phone | string
	 *
	 * @var string
	 */
	public string $type;
	public ?string $tableName;

	public function __construct(string $columnName, string $type, string $tableName = null)
	{
		$this->name = $columnName;
		$this->type = $type;
		$this->tableName = $tableName;
	}

	public function getFullColumnName(bool $fullyQualifiedName = false)
	{
		if ($this->tableName === null || $fullyQualifiedName === false)
			return $this->name;
		else
			return $this->tableName . '.' . $this->name;
	}
}

class Row
{
	public array $values = array();

	/**
	 * Providing an array will push each element of the array onto the variable stack.
	 *
	 * @param mixed $value
	 */
	public function __construct(array $data)
	{
		$this->values = $data;
	}

	public function getValues()
	{
		return $this->values;
	}

	public function addValue(mixed $data)
	{
		$this->values[] = $data;
	}
}

class OPWConnector extends DatabaseConnector
{
	/**
	 * Description
	 *
	 * @return array|false
	 */
	public function example()
	{
		return $this->select('SELECT * FROM table');
	}
}
