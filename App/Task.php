<?php
namespace App;

use PDO;
class Task
{
    public $id=null;
    private $sip;
    private $account;
    private $balance;
    private $error = array();
    public $table = 'seti';

    private function validate()
    {
        foreach (['sip', 'account', 'balance'] as $key) 
        {
            if(empty($this->$key))
            {
                echo $key;
                $this->error[$key] = "Это поле обязательно для ввода";
            }
        }

        if (!empty($this->error))
        {
            return false;
        }

        return true;
    }

    public static function get_pdo()
    {
        $_pdo;
        if (empty($_pdo))
        {
            $_pdo = new PDO('mysql:host=localhost;dbname=test','root',''); 
        }

        return $_pdo;
    }

    public function save_to_db()
    {
        $sql = static::get_pdo()->prepare('INSERT INTO `'.$this->table.'` (`sip`,`account`,`balance`) VALUES (?,?,?);');

        $sql->execute(array($this->sip, $this->account, $this->balance));

        return $sql->rowCount() === 1;
    }

    public function update_db($gid)
    {
        $sql = static::get_pdo()->prepare('SELECT * FROM `' . $this->table . '` where `id`='.$gid.';');
        $sql->execute();

        $object = $sql->fetchObject(static::class);
        $this->sip = isset($_GET['sip']) ? trim($_GET['sip']) : $object->sip;
        $this->account = isset($_GET['account']) ? trim($_GET['account']) : $object->account;
        $this->balance = isset($_GET['balance']) ? trim($_GET['balance']) : $object->balance;

        $sql = static::get_pdo()->prepare('UPDATE `'.$this->table.'` SET `sip`= ?, `account`= ?, `balance`= ? where `id`= ? limit 1;');
        $sql->execute(array($this->sip, $this->account, $this->balance, $gid));

        return $this->read_to_db();
    }

    public function read_for_balance()
    {
        $sql = static::get_pdo()->prepare('SELECT * FROM `' . $this->table . '`;');
        $sql->execute();

        $objects = [];
        $str = "{\n";
        while ($object = $sql->fetchObject(static::class))
        {
            if ($_GET['balance'] == "positiv" && $object->balance > 0) {
                $str .= "{id: ".$object->id.", sip: ".$object->sip.", account: ".$object->account.", balance: ".$object->balance."},\n";
            }

            if ($_GET['balance'] == "negativ" && $object->balance <= 0) {
                $str .= "{id: ".$object->id.", sip: ".$object->sip.", account: ".$object->account.", balance: ".$object->balance."},\n";
            }
            $objects[] = $object;
        }

        $str = substr($str,0,-2);
        $str .= '}';

        return $str;
    }

    public function validate_two()
    {
        if ($this->sip != trim($_POST['sip']) or $this->account != trim($_POST['account']) or $this->balance != trim($_POST['balance']))
        {
            return true;
        }

        return false;
    }

    public function read_to_db()
    {
        //"<script src = 'test.js'></script>";
        $sql = static::get_pdo()->prepare('SELECT * FROM `' . $this->table . '`;');
        $sql->execute();

        $objects = [];
        $str = "{\n";
        while ($object = $sql->fetchObject(static::class))
        {
            $str .= "{id: ".$object->id.", sip: ".$object->sip.", account: ".$object->account.", balance: ".$object->balance."},\n";
            $objects[] = $object;
        }

        $str = substr($str,0,-2);
        $str .= '}';

        return $str;
    }

    public function read_for_id($gid)
    {
        $sql = static::get_pdo()->prepare('SELECT * FROM `' . $this->table . '` where `id`='.$gid.';');
        $sql->execute();

        $object = $sql->fetchObject(static::class);
        echo $gid;
        $this->id = $gid;
        $this->sip = $object->sip;
        $this->account = $object->account;
        $this->balance = $object->balance;
        
        if (!isset($_GET['sip'])) {
            $_GET['sip'] = $object->sip;
        }
        if (!isset($_GET['account'])) {
            $_GET['account'] = $object->account;
        }
        if (!isset($_GET['balance'])) {
            $_GET['balance'] = $object->balance;
        }

        return $this->update_db();
    }

    public function insert()
    {
        $this->sip = isset($_GET['sip']) ? trim($_GET['sip']) : null;
        $this->account = isset($_GET['account']) ? trim($_GET['account']) : null;
        $this->balance = isset($_GET['balance']) ? trim($_GET['balance']) : null;

        var_dump($this->duration);

        if ($this->validate())
        {
            $this->save_to_db();
            return $this->read_to_db();
        }
        else {
            return "Validation error";
        }
    }

    public function del($gid)
    {
        $sql = $this->get_pdo()->prepare('DELETE FROM `'.$this->table.'` WHERE `id`='.$gid.';');
        $sql->execute();

        return $this->read_to_db();
    }
}
