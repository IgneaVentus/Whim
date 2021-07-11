<?php
    namespace Whim\Controllers;
    require "../vendor/autoload.php"; 

    class DBManipulator {
        public $conn;
        public $data;

        // Method for data preparation
        private function prepareData ($data_string) {
            // Remove all unneeded special symbols to remove possibility of data tampering
            $data_string=preg_replace("/[\[\]\*\{\}\(\)\+\^\&\.\\\?<>'\"]/s", "", $data_string);
            // Consolidate all double or longer whitespaces
            $data_string=preg_replace("/\s{2,}/s", " ", $data_string);
            // Explode the data into table
            $data = explode("|", trim($data_string));
            // Return prepared data
            return $data;
        }

        public function __construct ($data_string) {
            // Prepare data
            $data = prepareData($data_string);
            // Prepare connection
            $cm = new Whim\Controllers\ConnectionManager("./Data/settings.ini");
            $this->conn = $cm->conn;
        }

        public function insert($data){
            $conn->beginTransaction();
            try {
                // Find which table we need to use, then create new object of that table using data in url, then insert data
                switch (array_shift($data)) {
                    // Right now only one table exists
                    case "article": $article = new Whim\Models\Article($data[0],$data[1],$data[2]);
                        $article->insert($conn);
                        break;
                    default: throw new Exception("Table not found");
                }
                echo("Success!");
            }
            catch (Exception $e) {
                $conn->rollBack();
                die("ERROR: ".$e->getMessage());
            }
        }

        public function update($data){
            $conn->beginTransaction();
            try {
                // Find which table we need to use, then create new object of that table using data in url, then update data
                switch (array_shift($data)) {
                    // Right now only one table exists
                    case "article": $article = new Whim\Models\Article($data[1],$data[2],$data[3]);
                        $article->update($conn, $data[0]);
                        break;
                    default: throw new Exception("Table not found");
                }
                echo("Success!");
            }
            catch (Exception $e) {
                $conn->rollBack();
                die("ERROR: ".$e->getMessage());
            }
        }
    }
?>