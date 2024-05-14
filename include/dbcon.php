<?php
// Just activates JavaScript Cross-Site Scripting Protection on modern browsers for an extra level of security
header('X-XSS-Protection: 1; mode=block');
class Database {
    /**
     * Class for Basic Database Querying
     */

     /**
      *  @var PDO $pdo Instance of the PDO Class
      */
    private $pdo;

    public function __construct($hostname, $username, $password, $database) {
        /**
         * Constructs a new PDO instance.
         * Sets Error mode as ERRMODE_EXCEPTION.
         * Sets Fetch mode as FETCH_ASSOC.
         * 
         * @param string $hostname - The connection hostname.
         * @param string $username - Username for the database account.
         * @param string $password - Password for the database account.
         * @param string $database - Database name to connect to.
         */
        try {
            $this->pdo = new PDO("mysql:host=$hostname;port=3306;dbname=$database;charset=utf8mb4",
            $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Could not connect to database $database :" . $e->getMessage());
        }
    }

    public function select(string $table, string $columns="*", string $where="", array $params = array(), string  $condition="", bool $fetchAll = true) {
        /**
         * Function to construct and execute SELECT statements.
         * 
         * @param string $table - Database table you want to select from.
         * @param string $columns - Columns you would like to select. Default is "*".
         * @param string $where - Used for a WHERE condition. Default is "".
         * @param array $params - Associated array to satify the where condition. Default is [].
         * @param bool $fetchAll - boolean to use fetchAll() (true) or fetch() (false). Default is true.
         * @param string $condition - used to add a condition to the statement such as ORDER BY. Default is "".
         * 
         * @return array|null Returns array containing the fetched data, or null upon failure.
         */
        $sql = "SELECT $columns FROM $table";

        if (!empty($where)) {
            $sql .= " WHERE $where";
            $params = sanitizeArray($params);
        }
        if (!empty($condition)) {
            $sql .= " $condition";
        }
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }

        if ($fetchAll === true) {
            return $stmt->fetchAll();
        } else {
            return $stmt->fetch();
        }
    }

    public function insert(string $table, array $data): ?int {
        /**
         * function to construct and execute INSERT statements.
         * 
         * @param string $table - Database table you want to insert into.
         * @param array $data - Associative Array where, 
         *              keys refer to database columns, values refer to data to be inserted.
         * 
         * @return int|null Returns last inserted ID, or null upon failure
         */
        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        $sanitizedData = sanitizeArray($data);

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($sanitizedData);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }

        return $this->pdo->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $params = array()) { 
        /**
         * Function to construct and execute UPDATE statements
         * 
         * @param string $table - Database table you want to update
         * @param array $data - Associative Array where,
         *                      Key refer to database columns you want to change, values refer to data you want to be changed to
         * @param string $where - set the where condition of the statement
         * @param array $params - Associative Array that is used to statify the where condition specified
         * 
         * @return int|null - Returns Integer of affected Rows, or null upon failure
         */
        $set = "";
        $sanitizedData = sanitizeArray($data);
        foreach ($sanitizedData as $key => $value) {
            $set .= "$key = :$key, ";
        }
        $set = rtrim($set, ", ");
        $sql = "UPDATE $table SET $set WHERE $where";

        $params = array_merge($sanitizedData, $params);
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
        return $sql;
    }

    public function delete(string $table, string $where, array $params = array()): ?int {
        /**
         * Function to construct and execute DELETE statements.
         * 
         * @param string $table - Database table you want to delete from.
         * @param string $where - Used for WHERE condition.
         * @param array $params - Used to facilitate WHERE condition.
         * 
         * @return int|null - Returns Integer of affected Rows, or null upon failure
         */
        try {
            $sql = "DELETE FROM $table WHERE " . $where;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }

        return $stmt->rowCount();
    }
}
function generateRandomID(int $length = 16) {
    /**
     * Function to generate a random ID of a specfied character length.
     * 
     * @param int $length - Size of the String you would like to be generated. Default is 16.
     * 
     * @return string Returns String of specified length consisting of Numbers 0 - 9, Lowercase and Uppercase letters.
     */
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function sanitizeArray($data) {
    /**
     * Sanitize each value in an associative array using htmlspecialchars().
     * 
     * @param array $data - Associative array to be sanitized.
     * 
     * @return array - Sanitized associative array.
     */
    $sanitized_data = array();
    foreach ($data as $key => $value) {
        // Sanitize each value using htmlspecialchars()
        $sanitized_data[$key] = is_array($value) ? sanitizeArray($value) : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    return $sanitized_data;
}

// Default Database Stuff needed for most if not all pages
$db = new Database($hostname = "localhost",
$username = "mark",
$password = "Skylaluke098.",
$database = "VGEmporium");

/** 
 * -------------------------------------------------------------------------------------------------
 * | For when i do an oopsie and forget password                                                   |
 * -------------------------------------------------------------------------------------------------
 * | $passwordHash = password_hash("Markcanty", PASSWORD_DEFAULT);                                 |
 * | $db->update("users", ["password" => $passwordHash], "user_id = :user_id", [":user_id" => 3]); |
 * -------------------------------------------------------------------------------------------------
*/


session_start();
// Here as will be checked on each page. Logs person in if Cookie is set - Only checks once
if (!isset($_SESSION["userId"])) {
    if (isset($_COOKIE['session'])) {
        $currentDate = date("Y-m-d H:i:s");
        $row = $db->select("sessions",
        "user_id",
        "session_id = :session_id AND expiry_date >= :expiry_date",
        [":session_id" => $_COOKIE['session'],":expiry_date" >= $currentDate]);
    
        if (!empty($row)) {
            $_SESSION['userId'] = $row['user_id'];
            } 
        }
}
// Grabs user ID and checks to see if User is an Admin - Only checks once
if (!isset($_SESSION["isAdmin"])) {
    if (isset($_SESSION["userId"]))
    $row = $db->select("users", "role_id", "user_id = :user_id", [":user_id" => $_SESSION['userId']], "", false);
    if (!empty($row)) {
        if ($row["role_id"] === 2 | $row["role_id"] === 3) {
            $_SESSION["isAdmin"] = true;
        } else {
            $_SESSION["isAdmin"] = false;
        }
    }
}