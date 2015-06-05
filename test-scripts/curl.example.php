<?php 

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$proxy = '129.47.16.13:3128';
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_URL, "www.whereismypower.co.za/api/get_status"); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$output = curl_exec($ch); 
curl_close($ch); 

$result = json_decode($output);
if (empty($result->active_stage)) {
    $stage = 'None';
    $bgColor = '34D280';
    $fontSize = '20pt';
    $marginTop = "margin-top: 20px;";
} else {
    $stage = $result->active_stage;
    $bgColor = 'CD3333';
    $fontSize = '50pt';
    $marginTop = '';
}
$stageName = $result->active_stage_name;

?>
<!--  you get a 500 x 250 px div to display your announcement in -->
<style type="text/css">
    .eskomDiv {
        width: auto;
        height: auto;
        overflow: hidden;
        margin: auto;
        text-align: center;
        font-size: <?php echo $fontSize; ?>;
        background-color: #<?php echo $bgColor; ?>;
    }
    .eskomWrapper {
        width:auto;
        height:auto;
        overflow: hidden;
        color: #FFF;
        padding:20px;
        padding-top:0px;
        padding-bottom:0px;
    }
</style>
<div class="eskomWrapper">
    <div class="eskomDiv" style="padding-top:40px;<?php echo $marginTop; ?>"><img src="announcements/eskom.png" width="190" height="50"  /></div>
    <div class="eskomDiv" style="padding-bottom:40px;"><?php echo $stageName; ?></div>
</div>