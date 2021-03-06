<?php
require_once "../../../resources/config.php";
require_once "../bcrypt/Bcrypt.php";
include ('../../../resources/classes/Popover.php');

session_start();

$per_id = htmlspecialchars($_POST['per_id'], ENT_QUOTES);
$uname = htmlspecialchars($_POST['uname'], ENT_QUOTES);
$password = htmlspecialchars($_POST['password'], ENT_QUOTES);
$last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES);
$first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES);
$mname = htmlspecialchars($_POST['mname'], ENT_QUOTES);
$position = htmlspecialchars($_POST['position'], ENT_QUOTES);
$access_type = htmlspecialchars($_POST['access_type'], ENT_QUOTES);
$accnt_status = htmlspecialchars($_POST['accnt_status'], ENT_QUOTES);

$pwCheck = "SELECT * from personnel where per_id = '$per_id';";
$resultCheckPw = DB::query($pwCheck);

if (count($resultCheckPw) > 0) {
  foreach ($resultCheckPw as $row) {
        $verifypw = Bcrypt::checkPassword($password, $row['password']);
        if ($verifypw == TRUE) { //
            $delstmnt = "DELETE FROM PERSONNEL WHERE per_id = '$per_id'";
            DB::query($delstmnt);

            //USER LOGS
            date_default_timezone_set('Asia/Manila');
            $per_act_msg= "DELETED PERSONNEL ACCOUNT : $per_id";
            $username = $_SESSION['username'];
            $currTime = date("h:i:s A");
            $log_id = null;
            $currDate = date("Y-m-d");
            $accnt_type = $_SESSION['accnt_type'];

            DB::insert('user_logs', array(
              'log_id' => $log_id,
              'user_name' => $username,
              'time' => $currTime,
              'log_date' => $currDate,
              'account_type' => $accnt_type,
              'user_act' => $per_act_msg,
            ));

            $alert_type = "danger";
            $message = "Personnel Account Deleted";
            $popover = new Popover();
            $popover->set_popover($alert_type, $message);
            $_SESSION['success_personnel_delete'] = $popover->get_popover();
            header("location: ../personnel_list.php");
        }
        else {
            $alert_type = "danger";
            $error_message = "Cannot Delete Personnel: Incorrect Password";
            $popover = new Popover();
            $popover->set_popover($alert_type, $error_message);
            $_SESSION['incorrect_pw_del'] = $popover->get_popover();
            header("location: ../personnel_view.php?per_id=$per_id");
        }
    }
}

?>
