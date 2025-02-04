<?php
    $dir_name = __DIR__;
    require_once(realpath($dir_name.'/vendor/autoload.php'));
    $server_ip = $_SERVER['REMOTE_ADDR'];
    $dotenv = new Dotenv\Dotenv($dir_name);
    $dotenv->load();
    $whitelist = array('127.0.0.1', '::1');

    if (!in_array($server_ip, $whitelist)) {
        $db_username = $_ENV['USER'];
        $db_password = $_ENV['PASSWORD'];
        $db_name = $_ENV['DATABASE'];
        $db_host = $_ENV['HOST'];
    } else {
        $db_username = "root";
        $db_password = "";
        $db_name = "facebook";
        $db_host = "localhost";
    }

    class Database {
        public $string_where = "";
        
        function connect() {
            return mysqli_connect($GLOBALS['db_host'], $GLOBALS['db_username'], $GLOBALS['db_password'], $GLOBALS['db_name']);
        }

        function disconnect($db) {
            return mysqli_close($db);
        }

        function deleteRow($table) {
            $db = self::connect();
            $query = "DELETE FROM $table".$this->string_where;
            mysqli_query($db, $query);
            self::disconnect($db);
            $this->string_where = "";
            return true;
        }

        function join($join_type = "LEFT", $table_name, $condition1, $condition2, $rename_table = NULL) {
            if ($rename_table == NULL) {
                $this->string_where.=" $join_type JOIN $table_name ON $condition1=$condition2";
            } else {
                $this->string_where.=" $join_type JOIN $table_name AS $rename_table ON $condition1=$condition2";
            }
        }

        function orderBy($column, $type = "ASC") {
            $this->string_where.=" ORDER BY $column $type";
        }

        function deleteColumn($table, $column) {
            $db = self::connect();
            $query = "ALTER TABLE $table DROP COLUMN $column";
            mysqli_query($db, $query);
            self::disconnect($db);
            return true;
        }

        function addColumn($table, $column, $type, $value = NULL) {
            $db = self::connect();
            if ($type == "VARCHAR") {
                $value = 64;
            } elseif ($type == "INT") {
                $value = 11;
            }
            $query = "ALTER TABLE $table ADD COLUMN $column $type($value)";
            mysqli_query($db, $query);
            self::disconnect($db);
            return true;
        }

        function select($table, $limit = NULL) {
            $db = self::connect();
            $limit_text = NULL;
            if ($limit != NULL) {
                $limit_text.=" LIMIT $limit";
            }
            $query = "SELECT * FROM $table".$this->string_where.$limit_text;
            $data = mysqli_query($db, $query);
            $returndata = array();
            while ($row = mysqli_fetch_assoc($data)) {
                $returndata[] = $row;
            }
            self::disconnect($db);
            $this->string_where = "";
            return $returndata;
        }

        function update($table, $data) {
            $db = self::connect();

            $keys = array_keys($data);
            $values = array_values($data);
            $string_values = "";

            for ($i=0;$i<count($keys);$i++) {
                if (strlen($string_values) == 0) {
                    $string_values.="".$keys[$i]."='".$values[$i]."'";
                } else {
                    $string_values.=", ".$keys[$i]."='".$values[$i]."'";
                }
            }

            $query = "UPDATE $table SET $string_values".$this->string_where;
            mysqli_query($db, $query);
            self::disconnect($db);
            $this->string_where = "";
            return true;
        }

        function where($key, $value) {
            if (strlen($this->string_where) == 0) {
                $this->string_where.=" WHERE $key='$value'";
            } else {
                $this->string_where.=" OR $key='$value'";
            }
        }

        function insert($table, $data) {
            $db = self::connect();

            $keys = array_keys($data);
            $string_keys = "";
            for ($i=0;$i<count($keys);$i++) {
                if (strlen($string_keys) == 0) {
                    $string_keys.=$keys[$i];
                } else {
                    $string_keys.=", ".$keys[$i];
                }
            }

            $values = array_values($data);
            $string_values = "";
            for ($i=0;$i<count($values);$i++) {
                if (strlen($string_values) == 0) {
                    $string_values.="'".$values[$i]."'";
                } else {
                    $string_values.=", '".$values[$i]."'";
                }
            }

            $query = "INSERT INTO $table ($string_keys) VALUES ($string_values)";
            mysqli_query($db, $query);
            self::disconnect($db);
            return true;
        }

        function query($query) {
            $db = self::connect();
            $data = mysqli_query($db, $query);

            $returndata = array();
            while($row = mysqli_fetch_assoc($data)) {
                $returndata[] = $row;
            }
            self::disconnect($db);
            return $returndata;
        }
    }
?>