<?php

class civicrmActivity {
    var $id;
    var $activity_date_time;
    var $status;
    
    function updateActivity()
    {
        $activity['version'] = 3;
        $activity['id'] = $id;
        $activity['activity_date_time'] = $activity_date_time;
        if ($status=="COMP") $activity['status_id'] = 2;
        $activitySaveResult = civicrm_api('Activity','Create',$activity);
        echo "<pre />";
        print_r($activitySaveResult);
    }   
}

?>