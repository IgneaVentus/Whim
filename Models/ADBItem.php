<?php
    namespace Whim\Models;

    abstract class ADBItem {
        protected $tablename; // Name of used DB table
        protected $instructions; // Table with INSERT and UPDATE statements
        protected $data; // Table containing whole DB data

        abstract function __construct();

        abstract function __set($name, $value);

        // Main function for fetching data from DB
        public function fetch ($conn, $columns, $where_args, $limit, $offset) {
            // Check if provided connection isn't empty
            if ($conn!=0||$conn!=null) throw new Exception("No connection specified.");
            // Check if provided columns list isn't empty, if it is, get all columns
            if ($columns==0||$columns==null) $columns="*";
            // Prepare beggining of query
            $select = "SELECT ".$columns." FROM ".$this->tablename;
            // Check if there are arguments for WHERE option, if they are, add them to query
            if ($where_args!=0||$where_args!=null) $select .= " WHERE ".$where_args;
            $select.= " ORDER BY id";
            // Check if there are limits and offsets, if there are, add them to query
            if ($limit!=0||$limit!=null) $select.=($offset!=0||$offset!=null)? " LIMIT ".$offset.", ".$limit : " LIMIT ".$limit;
            $select .= ";";

            // Prepare query for execution and proceed
            if (!($stmt = $conn->prepare($select))) throw new Exception("Error during preparation of DB query.");
            $stmt->execute();

            // Return data received from query
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

        // Simple interface for fetching all rows at once without filling all fetch args
        public function fetchAll ($conn) {
            return Fetch($conn, 0, 0, 0, 0);
        }

        // Main update and insert function
        private function inject ($conn, $type) {
            // Check if provided connection isn't empty
            if ($conn!=0||$conn!=null) throw new Exception("No connection specified.");
            // Cheking if data isn't empty with exception of ID
            $is_data=true;
            foreach($this->data as $key=>$val) {
                if($key!="id" && $val==nul) $is_data=false;
            }
            if ($is_data) {
                // Preparation of statement according to given type
                if (!($stmt = $conn->prepare($this->instructions[$type]))) throw new Exception ("Statement preparation failed.");

                // Bind values
                foreach ($this->data as $key => $val) {
                    if ($key=="id"&&$type==0) continue; // Skip ID binding for INSERT
                    if (is_numeric($val)) { if (!($stmt->bindValue(":".$key, $val, PDO:PARAM_INT))) throw new Exception ("Error during value binding."); }
                    else if (is_bool($val)) { if (!($stmt->bindValue(":".$key, $val, PDO:PARAM_BOOL))) throw new Exception ("Error during value binding."); }
                    else if (is_string($val)) { if (!($stmt->bindValue(":".$key, $val, PDO:PARAM_STR))) throw new Exception ("Error during value binding."); }
                }

                // Data injection, if successful return ID
                if (!($stmt->execute())) {
                    throw new Exception ("Error during injection");
                }
                else {
                    if ($type==0) $this->data["data"]=$conn->lastInsertId();
                }
            }
            else throw new Exception ("Error. Injection failed, no data given.");
        }

        // Simple interface for INSERT
        public function insert($conn) {
            $this->inject($conn,0);
        }

        // Simple interface for UPDATE
        public function update($conn, $id) {
            if (is_numeric($id)) {
                $this->data["id"] = $id;
                $this->inject($conn,1);
            }
            else throw new Exception ("ID invalid.");
        }

        // Remove row by id 
        public function remove($conn, $id) {
            if (is_numeric($id)) {
                $conn->exec("DELETE FROM ".$this->tablename." WHERE id=".$id.";" );
            }
            else throw new Exception ("ID invalid.");
        }

        abstract function createTable($conn);

        public function deleteTable($conn) {
            $conn->exec("DROP TABLE IF EXISTS ".$this->tablename.";");
        }

    }
?>