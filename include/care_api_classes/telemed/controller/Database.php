<?php 

class Database
{	

	public static $db;
    private $_error ;
    private static $statement;
    private $primaryKey;

	public static function getInstance(){
        if(self::$db == null){
            $CONFIG = include __DIR__ . '/../lib/telemed-config.php';
            self::$db = new PDO(
                "mysql:host={$CONFIG['DB_HOST']};port=3306;dbname={$CONFIG['DB_NAME']}",
                $CONFIG['DB_USER'],
                $CONFIG['DB_PASS'],
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
            );
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

	public function transaction(){
		self::$db->beginTransaction();
	}

	public function rollback(){
        self::$db->rollBack();
	}

    public function commit(){
        self::$db->commit();
    }

    public function getStatementInst(){
        self::$statement;
    }

    public function getModifyDate($additionalFormat = ""){
	    return date('Y-m-d h:m:s'.$additionalFormat);
    }

    public function getCreateDate($additionalFormat = ""){
        return $this->getModifyDate($additionalFormat);
    }

    public function closeConnection(){
        self::$db = null;
    }

	public function runQuery($query,$paramArray = array()){
        self::$statement = self::$db->prepare($query);
        return self::$statement->execute($paramArray);
    }	
    
    public function insert($query,$paramArray = array()){
        $result = $this->runQuery($query, $paramArray);
        return $result;
    }	
    
    public function lastInsertId()
    {
        return $this->primaryKey;
    }

    public function lockTable($table)
    {
        self::$db->exec("LOCK TABLES {$table}");
    }

    public function unLockTable()
    {
        self::$db->exec("UNLOCK TABLES");
    }

    public function uuid()
    {
        $resp = $this->getOne('SELECT UUID() as uuuid');
        return $resp['uuuid']."-".rand(1,999);
    }

    public function getError(){
        return self::$statement->errorInfo();
    }

	public function runSelect($query,$paramArray = array()){
        self::$statement = self::$db->prepare($query);
        self::$statement->execute($paramArray);
        self::$statement->setFetchMode(PDO::FETCH_ASSOC);
        return self::$statement->fetchAll();

	}


    public function getOne($query,$paramArray = array()){
        self::$statement = self::$db->prepare($query);
        self::$statement->execute($paramArray);
        self::$statement->setFetchMode(PDO::FETCH_ASSOC);
        return self::$statement->fetch();

    }


}	