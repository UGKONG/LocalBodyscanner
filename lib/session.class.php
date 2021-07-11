<?php
/**
 * 세션 관리 클래스 정의
 */
class Session {
	const SESSION_NOT_STARTED = false;
	const SESSION_STARTED = true;
	private static $sessionState = self::SESSION_NOT_STARTED;

	public function __construct() {
		if (self::$sessionState == self::SESSION_NOT_STARTED) {
			if (session_start()) {
				self::$sessionState = self::SESSION_STARTED;
			}
		}
	}

	/**
	 * Stores data in the session.
	 * Example: $session_instance->foo = 'bar';
	 *
	 * @param	name	Name of data
	 * @param	value	Value of data
	 * @return	void
	 */
	public function __set($name, $value) {
		$_SESSION[$name] = $value;
	}

	/**
	 * Gets data from the session.
	 * Example:echo  $session_instance->foo;
	 *
	 * @param	name	Name of data to get
	 * @return	mixed	Data stored in session
	 */
	public function __get($name) {
		if (isset($_SESSION[$name])) {
			return $_SESSION[$name];
		}
	}

	public function __isset($name) {
		return isset($_SESSION[$name]);
	}

	public function __unset($name) {
		unset($_SESSION[$name]);
	}

	/**
	 * Closes the session writing.
	 * $_SESSION can still be read, but writing will not update the session.
	 * The lock is removed and other scripts can now read the session.
	 */
	public function close() {
		session_write_close();
	}

	/**
	 * Destroys the session.
	 *
	 * @return	bool	TRUE if successfully destroyed
	 */
	public function destroy() {
		if (self::$sessionState == self::SESSION_STARTED) {
			return session_destroy();
			self::$sessionState = self::SESSION_NOT_STARTED;
		}
	}

} // End of class

class Database {
	private static $db_host = '192.168.0.12';
	private static $db_user = 'liansoft';
	private static $db_pass = 'liansoft1!';
	//private static $db_name = 'liansoft2';
	private static $db_name = 'jsw_test';
	//private static $db_user = 'kitech01';
	//private static $db_pass = 'kitech0102';
	//private static $db_name = 'kitech01';

	private $dbh;
	private $error;
	private $stmt;

	/**
	 * 기본 생성자.
	 *
	 * 정해진 데이터베이스 접속정보로 연결한다.
	 */
	public function __construct() {
		$dsn = 'mysql:host=' . self::$db_host . ';dbname=' . self::$db_name;
		/**
		 * Persistent connection 방식은 장점보다 단점이 위험성이 커서 사용하지 않음
		 */
		//$options = array(
		//	PDO::ATTR_PERSISTENT => true,
		//	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		//);

		try {
			//$this->dbh = new PDO($dsn, $this->db_user, $this->db_pass, $options);
			$this->dbh = new PDO($dsn, self::$db_user, self::$db_pass);
		} catch(PDOException $e) {
			$this->error = $e->getMessage();
		}
	}

	/**
	 * 기본 소멸자
	 *
	 * 데이터베이스 연결 정보를 해제한다.
	 */
	public function __destruct() { $this->dbh = null; }

	public function prepare($query) {
		try {
			$this->stmt = $this->dbh->prepare($query);
		} catch(PDOException $e) {
			$this->error = $e->getMessage();
		}
	}

	// TODO: bindValue 와 bindParam 을 구분하여 상세화 추가 확장 필요
	public function bind($param, $value, $type = null) {
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		$this->stmt->bindValue($param, $value, $type);
	}

	public function execute() {
		try {
			return $this->stmt->execute();
		} catch(PDOException $e) {
			$this->error = $e->getMessage();
		}
	}

	public function query($query) {
		$this->stmt = $this->dbh->prepare($query);
		return $this->stmt->execute();
	}

	public function fetch() { return $this->stmt->fetch(PDO::FETCH_ASSOC); }
	public function fetchAll() { return $this->stmt->fetchAll(PDO::FETCH_ASSOC); }
	public function rowCount() { return $this->stmt->rowCount(); }
	public function lastInsertId() { return $this->dbh->lastInsertId(); }
	public function getMessage() { return $this->error; }
} // end of Class

?>