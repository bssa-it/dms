<?php

 /**
 * @description
 * This script loads the acknowledgement widget details onto the dashboard.
 * 
 * @author      Chezre Fredericks
 * @package     Donor Management System
 * @copyright   None
 * @version     3.0.1
 * 
 * @Changes
 * 21/05/2014 - Chezre Fredericks:
 * File created
 * 
 * @availableVariables
 * $qtrNo   :   is the current quadrant number
 * $w       :   is the widget class object for the current widget
 * 
 */


ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

include("../../inc/globals.php");
$xmlFromDate = (empty($_GET['fromDate'])) ? null :$_GET['fromDate'];
$totalOutstanding = 0;

if (!empty($xmlFromDate)) {

    $bamMembershipTypeId = (int)$GLOBALS['xmlConfig']->bam->civiMembershipTypeId;

    #   LOAD USER'S DEPARTMENTS
    $myDepartments = $_SESSION['dms_user']['config']['departments'];
    #   BUILD DEPARTMENT LIST FOR THE SQL WHERE CLAUSE
    $whereDepts = " AND SUBSTR(T.`trns_organisation_id`,1,1) IN (";
    if (!empty($myDepartments)) {
        $isFirstDepartment = true; 
        foreach ($myDepartments as $k=>$v) {
            $whereDepts .= ($isFirstDepartment) ? "'".$k."'":",'".$k."'";
            $isFirstDepartment = false;
        } 
    }
    $whereDepts .= ')';
    #   GET THE USER'S START DATE FROM THE USER'S WIDGET CONFIG FILE
    $sql = "
    SELECT
        YEAR(`trns_date_received`) `year`,
        MONTH(trns_date_received) `month`,
        trns_region_id,
        COUNT(*) `unacknowledged`
    FROM 
        dms_transaction T 
        INNER JOIN `civicrm`.`civicrm_contact` C ON C.id = civ_contact_id
    WHERE 
        trns_date_received >= '$xmlFromDate' $whereDepts 
        AND T.`dms_must_acknowledge` = 'Y' 
        AND T.`trns_dnr_acknowledged` = 'N'
        AND NOT EXISTS (SELECT ack_trns_id FROM dms_acknowledgement WHERE ack_trns_id = trns_id)
    GROUP BY 1,2,3
    ORDER BY 1,2,3;";
    #echo '<div style="display:none">'.$sql.'</div>';
    
#   RETRIEVE THE DATA FROM THE DMS DATABASE
    $totalData = $GLOBALS['functions']->GetDataFromSQL($sql);

    #   POPULATE THE GRAPH DATA AND DISPLAYED VARIABLES
    $dataSource = '';
    $currMonth = 0;
    $currYear = 0;
    $regionsArray = array();
    $seriesDescriptions = '';
    if (!empty($totalData)) {
        foreach ($totalData as $k=>$v) {
            if ($v['year']!=$currYear || $v['month']!=$currMonth) {
                if (!empty($dataSource)) $dataSource .= ' },';
                $dataSource .= "\n" . '{ month: "'.date("M",strtotime(date($v['year']."-".$v['month']."-01"))).'"';
            } 
            $dataSource .= ', ' . $v['trns_region_id'] . ': ' . $v['unacknowledged'];

            $currYear = $v['year'];
            $currMonth = $v['month'];
            $totalOutstanding += $v['unacknowledged'];
            $regionsArray[$v['trns_region_id']] = $GLOBALS['functions']->GetRegionName($v['trns_region_id']);
        }
        $dataSource .= ' }';
        foreach ($regionsArray as $r=>$d) {
            if (!empty($seriesDescriptions)) $seriesDescriptions .= ',';
            $seriesDescriptions .= '{ valueField: "'.$r.'", name: "'.$d.'" }';
        }
    }
}
?>
<div id="summaryChartDiv">
    <?php if ($totalOutstanding>0) { ?>
        <div id="unAcknowledgedChart" style="width:330px;height:200px;padding: 10px;color: #FFF"></div>
        <script type="text/javascript">
            var dataSource = [
                <?php echo $dataSource; ?>
            ];
            
            $("#unAcknowledgedChart").dxChart({
                dataSource: dataSource,
                commonSeriesSettings: {
                    argumentField: "month",
                    type: "stackedBar",
                    showInLegend: false,
                    font: { color: "#FFF" }
                },
                series: [
                    <?php echo $seriesDescriptions; ?>
                ],
                commonAxisSettings: {
                    color: 'white',
                    grid: {
                        color: 'white'
                    },
                    label: {
                        font: { color: 'white' }
                    }
                },
                tooltip: {
                    enabled: true,
                    customizeText: function () {
                        return this.seriesName + ": " + this.valueText;
                    }
                }
            });
        </script>
        <style type="text/css">
            #ackSummaryDiv {
                width: 200px;
                height: auto;
                float: right;
                overflow: hidden;
                padding-top: 15px;
            }
        </style>
    <?php } else { 
            $allFiles = glob("img/*mp4");
            $userWidgetConfig = simplexml_load_file($_SESSION['dms_user']['userid'].".acknowledgement.xml");
            $lastVideo = $userWidgetConfig->lastVideo;
            $video = '';
            while (empty($video)) {
                $videoId = rand(0, count($allFiles)-1);
                if (basename($allFiles[$videoId])!=$lastVideo) {
                    $video = basename($allFiles[$videoId]);
                    $userWidgetConfig->lastVideo = $video;
                    file_put_contents($_SESSION['dms_user']['userid'].".acknowledgement.xml", $userWidgetConfig->asXML());
                }
            }
        ?>
        <style type="text/css">
            #ackSummaryDiv {
                width: 220px;
                height: auto;
                float: right;
                overflow: hidden;
            }
        </style>
	   <video width="300" height="190" autoplay loop>
            <source src="widgets/acknowledgement/img/<?php echo $video; ?>" type="video/mp4">
           </video>
	<?php } ?>
    </div>
<div id="ackSummaryDiv">
    <div class="counterDisplayHeading" align="center">Outstanding</div>
    <div class="counterDisplay" align="center" id="cntDisplay"><?php echo $totalOutstanding; ?></div>
    <div class="btn" onclick="<?php if ($totalOutstanding>0) { ?> window.location='acknowledgement.php'<?php } else { ?> alert('No outstanding acknowledgements'); <?php } ?>">Start Acknowledgements</div>
</div>