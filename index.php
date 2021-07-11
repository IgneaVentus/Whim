<?php
    namespace Whim;

    $request_uri = explode("?", $_SERVER["REQUEST_URI"], 2);

    switch ($request_uri[0]) {
        case "/":
            require "Views/Home.html";
            break;
        default :
            phpinfo();
            break;
    }

?>