<?php
class admin {
  public $ErrorMsg;

  public function CreateAdmin($username, $password) {
    if ($username != '' && strlen($password) > 5) {
      require "../includes/config.php";

      // Add admin
      $sql = "INSERT INTO maker (username, password) VALUES ('$username', MD5('$password'))";
      @mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (". mysql_errno() . ") " . mysql_error());

    } else {
      $this->ErrorMsg = "<div class=\"infobox error\">You must type a username and the password at least 6 characters long.</div>";
    }
  }

  public function DeleteAdmin($id) {
    require "../includes/config.php";

    $sql = "DELETE maker FROM maker WHERE id=$id";
    @mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());

    $this->ClearPermissions($id);
  }

  public function RenameAdmin($id, $newname) {
    require "../includes/config.php";

    $sql = "UPDATE maker SET username='$newname' WHERE id=$id";
    @mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());

    // Did you rename yourself? Fix session username if so.
    if ($id == $_SESSION ["sess_id"]) $_SESSION ["sess_user"] = $newname;
  }

  public function UniqueAdminName($username) {
    require "../includes/config.php";

    $sql = "SELECT username FROM maker WHERE username='$username'";
    $rows = mysql_num_rows(mysql_query($sql, $conn));

    // true is good. false is bad.
    if ($rows == 0) return true;
    else return false;
  }

  public function ClearPermissions($UserID) {
    require "../includes/config.php";

    $sql = "DELETE permissions FROM permissions WHERE uid = $UserID";
    //echo $sql . "<br />";
    @mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  }

  public function AddPermissions($UserID, $Section, $Granted) {
    require "../includes/config.php";

    // $Granted should always be > 0. But check anyway.
    if ($Granted > 0)
      $sql = "INSERT INTO permissions (uid, sid, permissions) VALUES($UserID, $Section, $Granted)";

    //echo $sql . "<br />";
    @mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  }

}
?>