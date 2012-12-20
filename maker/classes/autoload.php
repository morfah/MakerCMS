<?php
function __autoload($class_name) {
  if (file_exists('classes/' . $class_name . '.class.php')) require_once 'classes/' . $class_name . '.class.php';
  else {
    echo "Error: Could not find classes/" . $class_name . '.class.php';
    exit();
  }
}
?>