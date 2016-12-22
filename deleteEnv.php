<?php
include('inc/db.inc.php');
include('inc/functions.inc.php');
if (isset($_POST['action']))
{
    
        if ($db=fConnectDb()) {
            $id = $_GET['id'];
            if (!egEnvInUse($id,$db)) { //if the env is not in use.....
                
                /*
                 *Envs are not REALLY deleted
                 *They are disabled, to ensure data is not lost
                 */
                
                $sql = "UPDATE egaming_envs SET enabled='0' WHERE id=$id"; 
                $result = mysqli_query($db,$sql);
                if($result) {
                    echo '<script type="text/javascript">alert("Deletion successful.");</script>';
                    echo '<script type="text/javascript">close();</script>';
                }
                else {
                    echo 'Could not query database! ' . mysql_error($db);
                }
            }
            else {
                echo '<script type="text/javascript">alert("Environment is in use by a reservation. Environment not deleted.");</script>';
                echo '<script type="text/javascript">close();</script>';
            }
        }
        else {
            echo 'Could not connect to database! ' . mysql_error($db);
        }
}
?>