<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 2018/6/8
 * Time: 16:59
 */

session_start();
session_unset();
session_destroy();
echo json_encode(array('code' => 1));