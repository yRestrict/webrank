<?php
require_once 'inc/config.php';

function fl($m){
    $m = abs($m);
    $m = intval($m);
    return $m;
}

class DB
{
    protected static $instance = null;

    public function __construct() {}
    public function __clone() {}

    public static function instance()
    {
        if (self::$instance === null)
        {
            $opt  = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => TRUE,
            );
            $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHAR;
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, $opt);
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }

    public static function run($sql, $args = [])
    {
        if (empty($args))
        {
            return self::instance()->query($sql);
        }
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}

class MyPDO extends PDO
{
    public function run($sql, $args = NULL)
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}

function page($k_page = 1){ // Retorna a página atual
    $page = 1;
    if (isset($_GET['page'])) {
        if ($_GET['page'] == 'end') {
            $page = intval($k_page);
        } elseif (is_numeric($_GET['page'])) {
            $page = intval($_GET['page']);
        }
    }
    if ($page < 1) {
        $page = 1;
    }
    if ($page > $k_page) {
        $page = $k_page;
    }
    return $page;
}

function k_page($k_post = 0, $k_p_str = 10){ // Calcula o número de páginas
    if ($k_post != 0) {
        $v_pages = ceil($k_post / $k_p_str);
        return $v_pages;
    } else {
        return 1;
    }
}
?>
