<?php
namespace ZXUN\Data\DataBase;

//MySQL 数据库对象
class MySQL{
    var $DataBase;
    var $ConnInfo;
    var $Server;
    var $CONN;//链接对象

    //构造方法
    function __construct($db){

        $this->DataBase = $db;

        //链接变量
        $server = "";$userid = "";$password = "";$dbname = "";$port="";

        $connString = $this->DataBase["ConnectionString"];
        $info = explode(';',$connString);
        foreach($info as $item){
            $str = explode('=',$item);
            $key = trim(strtolower($str[0]));
            $value = trim($str[1]);
            switch($key){
                case  "server":
                case "data source":
                    $server = $value;
                    break;
                case  "database":
                case "initial catalog":
                    $dbname = $value;
                    break;
                case "password":
                case "pwd":
                    $password = $value;
                    break;
                case "user id";
                case "uid";
                    $userid = $value;
                    break;
                case "port":
                    $port = $value;
                    break;
            }
        }

        /*
        echo $server.'<br/>';
        echo $userid.'<br/>';
        echo $password.'<br/>';
        echo $dbname.'<br/>';*/

        $this->Server = $server;
        $this->ConnInfo = array (
            "UID" => $userid,
            "PWD" => $password,
            "Database" => $dbname,
            "Port"=>$port
        );

        //print_r($this->ConnInfo);
    }

    function Connection(){
        //$this->CONN = mysqli_connect('192.168.1.100','root','zxun.db8','qycft_cn_stock','3308');
        $this->CONN = mysqli_connect( $this->Server,$this->ConnInfo["UID"],$this->ConnInfo["PWD"],$this->ConnInfo["Database"],$this->ConnInfo["Port"]);
        mysqli_select_db($this->CONN,$this->ConnInfo["Database"]);
        mysqli_query($this->CONN, 'set names utf8');

        /* check connection */
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }

        if ($this->CONN) {
            // echo "Connection Success";
        } else {
            echo "<error>Server ".$this->Server." Connection failed</error>";
            exit;
        }
        return $this->CONN;
    }

    function Execute($func,$sql,$param=null){
        $result = null;
        if($param!=null) {
            //echo '1';
            if ($stmt = new _stmt($this->CONN,$sql)) {
                $i = 0;
                $arr = [];
                foreach($param as $key=>$value){
                    //print_r($key); 序号
                    if(gettype($value) == "array"){
                        foreach($value as $k=>$v){
                            //数据类型  参考 http://php.net/manual/en/mysqli-stmt.bind-param.php
                            //print_r("批量插入:".$k.'='.$v.'<br/>');
                            $arr[$i] = $v;
                            $stmt->mbind_param($this->gettype($v),$arr[$i]);
                            $i++;
                        }
                    }
                    else{
                        //print_r("单行插入:".$key.'='.$value.'<br/>');
                        $arr[$i] = $value;
                        //echo $this->gettype($value);
                        //exit;
                        $stmt->mbind_value($this->gettype($value),$value);
                        $i++;
                    }
                }
                /*print_r($arr);
                echo '<br/>';
                print_r($sql);
                exit;*/
                $stmt->execute();//执行命令

                //如果有错误
                if($stmt->errno > 0){
                    echo "ERROR:".$stmt->error;
                    exit;
                }
                //如果没有错误则继续执行
                switch($func){
                    case "Add":
                        return mysqli_insert_id($this->CONN);
                        break;
                    case "Delete":
                        break;
                    case "Update":
                        break;
                    case "Get":
                    case "GetList":
                    case "GetPageList":
                        $stmt->store_result();//获取结果集
                        //$num = $stmt->num_rows;//获取结果数量
                        $meta = $stmt->result_metadata();//获取字段名

                        $variables = array();$data = array();
                        while($field = $meta->fetch_field()) {
                            $variables[] = &$data[$field->name]; // pass by reference
                        }
                        call_user_func_array(array($stmt, 'bind_result'), $variables);
                        $i=0;
                        $result = [];
                        while($stmt->fetch())
                        {
                            $array[$i] = array();
                            foreach($data as $k=>$v) {
                                $array[$i][$k] = $v;
                                //print_r($v."<br/>");
                            }
                            array_push($result,$array[$i]);
                            $i++;
                        }
                        //print_r($arr);
                        break;
                }
                $stmt->close();//释放mysqli_stmt对象占用的资源
            }
        }
        else {
            //echo '2';
            try {
                if (!empty($_GET["debug"])) {
                    //print_r($sql.'<br/>');
                    //$sql = "upload cms_Article set ArticleID=123 where id=4356";
                    //print_r($sql);
                }
                $result = mysqli_query($this->CONN, $sql);
                if ($result === false) {
                    die(print_r("MSSQL Execute 执行错误"));
                }
            } catch (Exception $e) {
                print $e->getMessage();
            }
        }
        return $result;
    }


    private function gettype($value){
        $type = "s";
        switch(gettype($value)){
            case "double":
                $type = "d";
                break;
            case "integer":
                $type = "i";
                break;
        }
        return $type;
    }
}
?>