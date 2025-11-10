<?php
http_response_code(424);
$result = array(
    "message" => "Failed"
);
echo json_encode($result); //rest api

?>