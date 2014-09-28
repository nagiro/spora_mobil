<?php

final class Database {
	private static $instances = array();
	private $database;
    private $dbname;

	private function __construct($dbname = null) {
		try {
            $conn = $dbname? self::getConnectionString($dbname) : SQL_CONNECTION_STRING;

            $this->dbname = $dbname? $dbname : SQL_DBNAME;
			$this->database = new PDO($conn, SQL_USER, SQL_PASSWORD);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->database->query('SET NAMES utf8');

			return $this->database;
		} catch (PDOException $e) {
			Logger::append(Logger::LOG_DB, $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return false;
		}
	}

	private function __clone() {}

    private static function getConnectionString($name) {
        return 'mysql:dbname=' . $name . ';host=' . SQL_HOST ;
    }

    /**
     *
     * @param type $dbname
     * @return Database
     */
    public static function getInstance($dbname = null) {
        $dbname = $dbname? $dbname : SQL_DBNAME;

		if(!isset(self::$instances[$dbname])) {
			self::$instances[$dbname] = new Database($dbname);
        }

		return self::$instances[$dbname];
	}

    /**
     *
     * @param type $query
     * @return PDOStatement
     */
    public function getStatement($query) {
        try {
            return $this->database->prepare($query);
        } catch(Exception $e) {
			Logger::append(Logger::LOG_DB, $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return false;
        }
    }

	public function query($query, $params=null) {
		try {
			$db = $this->database->prepare($query);
			$db->execute($params);
			return $db->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			Logger::append(Logger::LOG_DB, $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return false;
		}
	}
	
	public function exec($query, $params=null) {
		try {
			$db = $this->database->prepare($query);

			return $db->execute($params);
		} catch (PDOException $e) {
			Logger::append(Logger::LOG_DB, $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return false;
		}
	}

    public function insert($query, $params=null) {
        try {
            $db = $this->database->prepare($query);
            $insertion = $db->execute($params);

            if($insertion === false) {
                throw new DBException('Could not get last insert ID: insertion failed');
            }

            return $this->database->lastInsertID();
        } catch (Exception $e) {
            Logger::append(Logger::LOG_DB, $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return false;
        }
    }

    public function tableExists($name) {
        try {
            $db = $this->database->prepare('SHOW TABLES LIKE :name');
            $db->execute(array(':name' => $name));

            $info = $db->fetchAll();

            return ( is_array($info) && isset($info[0]) );
        } catch(Exception $e) {
            return false;
        }
    }

    public function tableColumnExists($name, $field) {
        try {
            if(!$this->tableExists($name)) {
                return false;
            }

            $db = $this->database->prepare('SHOW COLUMNS FROM :name WHERE `Field` = :field');
            $db->execute(array(
                ':name' => $name,
                ':field'=> $field
            ));

            $info = $db->fetchAll();

            return ( is_array($info) && isset($info[0]) );
        } catch(Exception $e) {
            return false;
        }
    }
}

?>
