<?php
class content {
  public $LatestPageId;

  public function NewPage($sid, $vis, $dis, $context, $date, $header, $menuname, $headline, $is_url) {
    require "../includes/config.php";
    
    $startedby = $_SESSION ["sess_id"];
    $sql = "INSERT INTO content (sid, visible, disabled, context, startedby, startedby_date, header, menuname, headline, url) VALUES ($sid, $vis, $dis, '$context', $startedby, '$date', '$header', '$menuname', '$headline', $is_url)";
    //echo $sql."<br /><br />\n";
    @mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    
    // This assigns the correct order number for this newly added page.
    $sql = "SELECT COUNT(*) AS `rows`, MAX(`id`) AS `latest` FROM `content` WHERE `sid`=$sid AND `deleted`=0 GROUP BY `sid`;";
    $db = mysql_fetch_array(mysql_query($sql, $conn));
    $rows = $db ["rows"];
    $this->LatestPageId = $db ["latest"];
    $sql = "UPDATE `content` SET `order` = $rows WHERE `id` = $this->LatestPageId";
    //echo $sql."<br />\n";
    @mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  }

  public function UpdatePage($id, $sid, $movepage, $vis, $dis, $context, $date, $header, $menuname, $headline) {
    require "../includes/config.php";
    
    $updatedby = $_SESSION ["sess_id"];
    $order = "";
    //Are we are also moving the page to a diffrent section?
    if ($movepage != $sid) {
      $sql = "SELECT * FROM `content` WHERE `sid` = $movepage AND `deleted` = 0";
      $rows = mysql_num_rows(mysql_query($sql, $conn));
      $order = "`order` = " . ($rows + 1) . ", ";
    }
    $sql = "UPDATE content SET sid=$movepage, $order visible=$vis, disabled=$dis, context='$context', updatedby=$updatedby, updatedby_date='$date', header='$header', menuname='$menuname', headline='$headline' WHERE sid=$sid AND id=$id;";
    @mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());
  }

  public function DeletePage($id, $sid) {
    require "../includes/config.php";
    
    $sql = "UPDATE `content` SET `deleted` = 1, `order` = 0 WHERE `sid` = $sid AND `id` = $id";
    @mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    //echo "Deletion SQL: <br />\n$sql<br /><br />\nRe-ordering SQLS: <br />\n";
  }

  public function SortPages($sid) {
    require "../includes/config.php";
    
    $sql = "SELECT * FROM `content` WHERE `sid` = $sid AND `deleted` = 0";
    $rows = mysql_num_rows(mysql_query($sql, $conn));
    for ($i = 1; $i <= $rows; $i ++) {
      $sql_id = "SELECT `id` FROM `content` WHERE `sid` = $sid AND `deleted` = 0 ORDER BY `order` ASC LIMIT " . ($i - 1) . ",1";
      $fetch_id = mysql_fetch_array(mysql_query($sql_id, $conn));
      $sql = "UPDATE `content` SET `order` = $i WHERE `sid` = $sid AND `id` = " . $fetch_id ["id"];
      //echo $sql . "<br />";
      @mysql_query($sql, $conn) or die("<b>A fatal MySQL error occurred</b>.\n<br />\nError: (" . mysql_errno() . ") " . mysql_error());
    }
  }

  public function UniquePageName($header, $sid) {
    require "../includes/config.php";
    
    $sql = "SELECT header FROM content WHERE header='$header' AND sid=$sid";
    $rows = mysql_num_rows(mysql_query($sql, $conn));
    
    // true means that pagename is unique.
    if ($rows == 0) return true;
    else return false;
  }
}
?>