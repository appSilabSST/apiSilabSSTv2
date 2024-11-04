<?php

include('../valida_token.php');

if ($method == 'GET') {
    require('get.php');
} elseif ($method == 'POST') {
    require('post.php');
} elseif ($method == 'PUT') {
    require('put.php');
} elseif ($method == 'PATCH') {
    require('patch.php');
} elseif ($method == 'DELETE') {
    require('delete.php');
}
