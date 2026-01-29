<?php
if (preg_match('#^/api/test#', $_SERVER['REQUEST_URI'])) {
    header('Content-Type: text/plain');
    echo "API back OK";
    exit;
}