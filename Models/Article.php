<?php 
    namespace Whim\Models;

    class Article extends ADBItem {
        protected $tablename = "article";
        protected $instructions = 
        [ "INSERT INTO article (title, desc, status) VALUES (:title, :desc, :status)",
         "UPDATE article SET title=:title, desc=:desc, status=:status WHERE id=:id" ];
        protected $data = ["id"=>null, "title"=>"", "desc"=>"", "status"=>""];

        public function __construct() {
            $argv = func_get_args();
            if (func_num_args()==3) {
                $this->data["title"] = $argv[0];
                $this->data["desc"] = $argv[1];
                $this->data["status"] = $argv[2];
            }
            else if (func_num_args()==0) { }
            else throw new Exception ("Invalid number of arguments.");
        }

        // Value setter. In this case, not much checking is needed.
        public function __set($name, $value) {
            switch($name) {
                case "title": $this->data["title"]=$value; break;
                case "desc": $this->data["desc"]=$value; break;
                case "status": $this->data["status"]=$value; break;
                case "id": if(is_numerical($value)) { $this->data["id"]=$value; } break;
            }
        }

        public function present() {
            return $data;
        }

        public function createTable($conn) {
            $conn->exec(
                "CREATE TABLE ".$this->tablename." (
                    id integer primary key,
                    title text,
                    desc text,
                    status text
                );"
            );
        }
    }
?>