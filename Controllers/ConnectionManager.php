<?php
    namespace Whim\Controllers;

    class ConnectionManager {
        public PDO $conn;
        
        private function connectionSetter ($settings) {
            try {
                $conn = new PDO($settings["driver"].":host=".$settings["host"].":".$settings["port"].";dbname=".$settings["dbfilename"], $settings["username"], $settings["password"]);
            }
            catch (Exception $e) {
                throw new Exception ("Connection not estabilished:\n".$e->getMessage());
            }
        }

        public function __construct($file) {
            $settings = parse_ini_file($file, TRUE);
            connectionSetter($settings["database"]);
            initializeDB();
        }
        
        private function initializeDB () {
            $art1 = new Whim\Models\Article("The yellow stone","The yellow stone was thrown in the water, then simply dissapeared, sinking into the endless depth", "pending");
            $art2 = new Whim\Models\Article("Lorem ipsum","Doloret et amet poszła grupa na zamek", "rejected");
            $art3 = new Whim\Models\Article("Wieloryb","To majestatyczne i olbrzymie zwierzę śpiące do góry płetwami, co sprawia wrażenie jakby pod wodą znajdowały się olbrzymie, pływające kamienne kolumny", "accepted");
            $art1->dropTable($conn);
            $art1->createTable($conn);
            $art1->insert($conn);
            $art2->insert($conn);
            $art3->insert($conn);
        }
    }
?>