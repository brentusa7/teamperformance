<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>TOPDIAL - TEAM PERFORMANCE REPORT</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <link rel="stylesheet" type="text/css" media="all" href="daterangepicker.css" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>
    <body>
            <script>
                $( function() {
                    $( "#datepicker" ).datepicker();
                    $( "#datepicker2" ).datepicker();
                } );
            </script>
    <h1><center>TOPDIAL - TEAM PERFORMANCE REPORT</center></h1> 
    <form name="frm1" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
        <p>Dialing Date: <input type="text" id="datepicker" name="start_date"> to <input type="text" id="datepicker2" name="end_date">
        &nbsp;&nbsp;Select Campaign:
        <select name="campaign">
        <option value="Solar_Re" selected>Solar Revived</option>
        <option value="HomeWrty" selected>Home Warranty</option>
        </select>
        <input type="submit" name="submit" value="Submit"></p>
    </form> 

    <table class="table" id="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Agent Name</th>
                <th scope="col">Calls</th>
                <th scope="col">Leads</th>
                <th scope="col">Completed</th>
                <th scope="col">Contacts</th>
                <th scope="col">Contact Ratio(%)</th>
                <th scope="col">Nonpause Time</th>
                <th scope="col">System Time</th>
                <th scope="col">Talk Time</th>
                <th scope="col">Sales</th>
                <th scope="col">Sales Per Working Hour</th>
                <th scope="col">Sales Per Hour</th>
                <th scope="col">Callbacks</th>
            </tr>
        </thead> 
    <?php
        session_start();

        if(isset($_POST["submit"]))
        {
            $dt1 = $_POST["start_date"];
            $dt2 = $_POST["end_date"];
            $selected_campaign = $_POST["campaign"];
            $start = date("Y-m-d 00:00:00" , strtotime($dt1));
            $end = date("Y-m-d 23:59:59", strtotime($dt2));
            echo "<p><b>Time range: $start to $end</b></p>";
            include 'condb2.php';

            //Total calls
            $sql = "select b.full_name,count(*) as calls, a.campaign_id, a.user, a.lead_id, a.user_group, a.pause_type from vicidial_agent_log as a LEFT JOIN vicidial_users as b ON b.user = a.user WHERE a.event_time BETWEEN '".$start."' AND '".$end."' AND a.campaign_id='".$selected_campaign."' AND a.lead_id NOT IN ('NULL') AND a.user_group='5Strata' AND a.pause_type NOT IN ('SYSTEM') group by b.full_name";
            $result = $mysqli->query($sql);

            if($result->num_rows>0)
            {

                        echo '<tbody>';

                                while($row = $result->fetch_assoc())
                                {
                                       //total sales
                                        $totalsales = "select b.full_name,count(*) as sales, a.campaign_id, a.user from vicidial_agent_log as a LEFT JOIN vicidial_users as b ON b.user = a.user WHERE a.event_time BETWEEN '".$start."' AND '".$end."' AND a.campaign_id='".$selected_campaign."' AND a.status IN('Q1','Q2','Q3','Q4','Q5','Q6') AND b.full_name='".$row['full_name']."'";
                                        $resulttotal = $mysqli->query($totalsales);
                                        
                                        while($row1 = $resulttotal->fetch_assoc())
                                        {
                                            $sales = $row1['sales'];
                                        }  
                                        //completed
                                        $totalcompleted = "select b.full_name,count(*) as completed, a.campaign_id, a.user from vicidial_agent_log as a LEFT JOIN vicidial_users as b ON b.user = a.user WHERE a.event_time BETWEEN '".$start."' AND '".$end."' AND a.campaign_id='".$selected_campaign."' AND a.status IN('LB','DNC','ADC','BAD','NA','AB','B','NI1','NI2','NI3','NI4','NI5','NI6','NI7','NQ','Q1','Q2','Q3','Q4','Q5','Q6') AND b.full_name='".$row['full_name']."'";
                                        $resultcompleted = $mysqli->query($totalcompleted);
                                        
                                        while($rowcompleted = $resultcompleted->fetch_assoc())
                                        {
                                            $completed = $rowcompleted['completed'];
                                        }  
                                        //contacts
                                        $sqlcontact = "select b.full_name,count(*) as sales, a.campaign_id, a.user from vicidial_agent_log as a LEFT JOIN vicidial_users as b ON b.user = a.user WHERE a.event_time BETWEEN '".$start."' AND '".$end."' AND a.campaign_id='".$selected_campaign."' AND a.status IN('NI1','NI2','NI3','NI4','NI5','NI6','NI7','NQ','Q1','Q2','Q3','Q4','Q5','Q6','HUP','SCA','SCE','SCM') AND b.full_name='".$row['full_name']."'";
                                        $resultcontact = $mysqli->query($sqlcontact);

                                        while($row2 = $resultcontact->fetch_assoc())
                                        {
                                            $contact = $row2['sales'];
                                            $ratio = ($contact / $row['calls']) * 100;
                                            $ratioround = round($ratio ,2);
                                        } 
            
                                        //leads
                                        $leadsql = "select b.full_name,count(distinct a.lead_id,a.user) as leads, a.campaign_id, a.user, a.user_group, a.pause_type from vicidial_agent_log as a LEFT JOIN vicidial_users as b ON b.user = a.user WHERE a.event_time BETWEEN '".$start."' AND '".$end."' AND a.campaign_id='".$selected_campaign."' AND a.lead_id IS NOT NULL AND a.user_group='5Strata' AND b.full_name='".$row['full_name']."'";
                                        $resultlead = $mysqli->query($leadsql);
                                        while($row3 = $resultlead->fetch_assoc())
                                        {
                                            $totalleads = $row3['leads'];
                                        }


                                        //talk time
                                        $talktimesql = "select b.full_name,sum(talk_sec)-sum(dead_sec) as talktime, a.campaign_id, a.user, a.user_group, a.pause_type from vicidial_agent_log as a LEFT JOIN vicidial_users as b ON b.user = a.user WHERE a.event_time BETWEEN '".$start."' AND '".$end."' AND a.campaign_id='".$selected_campaign."' AND a.lead_id IS NOT NULL AND a.user_group='5Strata' AND a.pause_type NOT IN ('SYSTEM') AND b.full_name='".$row['full_name']."'";
                                        $resulttalktime = $mysqli->query($talktimesql);

                                        while($rowtalktime = $resulttalktime->fetch_assoc())
                                        {
                                            $talktime = $rowtalktime['talktime'];
                                            
                                            
                                        }

                                        //nonpause time
                                        $nonpausesql = "select b.full_name,SUM(talk_sec) + SUM(dispo_sec) + SUM(wait_sec) as nonpause_time, a.campaign_id, a.user, a.user_group, a.pause_type from vicidial_agent_log as a LEFT JOIN vicidial_users as b ON b.user = a.user WHERE a.event_time BETWEEN '".$start."' AND '".$end."' AND a.campaign_id='".$selected_campaign."' AND a.lead_id IS NOT NULL AND a.user_group='5Strata' AND a.pause_type NOT IN ('SYSTEM') AND b.full_name='".$row['full_name']."'";
                                        $resultnonpause = $mysqli->query($nonpausesql);

                                        while($rownonpause = $resultnonpause->fetch_assoc())
                                        {
                                                $nonpause = $rownonpause['nonpause_time'];
                                               
                                        }
                                        //sales per working hour        
                                        $sph = $nonpause / 3600 ; 
                                        $sphtotal = $sales / $sph;

                                        //sales per hour
                                        $sph2 = $talktime / 3600;
                                        $sph2total = $sales / $sph2;

                                        //nonpause convertion
                                        $hoursnonpause = floor($nonpause / 3600);
                                        $minutesnonpause = floor(($nonpause / 60) % 60);
                                        $secondsnonpause = $nonpause % 60;
                                        $nonpauseconverted = sprintf("%02d:%02d:%02d", $hoursnonpause, $minutesnonpause, $secondsnonpause);

                                        //talktime convertion
                                        $hoursnontalktime = floor($talktime / 3600);
                                        $minutestalktime = floor(($talktime / 60) % 60);
                                        $secondstalktime = $talktime % 60;
                                        $talktimeconverted = sprintf("%02d:%02d:%02d", $hoursnontalktime, $minutestalktime, $secondstalktime);
                                        

                                        //callbacks
                                       $callbacksql = "select b.full_name,count(*) as callbacks, a.campaign_id, a.user, a.status from vicidial_callbacks as a LEFT JOIN vicidial_users as b ON b.user = a.user WHERE a.campaign_id='".$selected_campaign."' AND a.status IN('LIVE','ACTIVE') AND b.full_name='".$row['full_name']."'";
                                      $resultcallbacksql = $mysqli->query($callbacksql);

                                      while($rownoncallbacks = $resultcallbacksql->fetch_assoc())
                                       {
                                                $callbacks = $rownoncallbacks['callbacks'];
                                               
                                      } 
                                        
                                    echo '<tr>';
                                    echo '<td>'.$row['full_name'].'</td>';
                                    echo '<td align="center">'.$row['calls'].'</td>';
                                    echo '<td align="center">'.$totalleads.'</td>';
                                    echo '<td align="center">'.$completed.'</td>';
                                    echo '<td align="center">'.$contact.'</td>';
                                  //  echo '<td align="center">'.number_format($ratioround, 2).'%</td>'; 
                                    echo '<td align="center">'.$ratioround.'</td>'; 
                                    echo '<td align="center">'.$nonpauseconverted.'</td>'; 
                                    echo '<td align="center">'.$nonpauseconverted.'</td>';
                                    echo '<td align="center">'.$talktimeconverted.'</td>';
                                    echo '<td align="center">'.$sales.'</td>';
                                    echo '<td align="center">'.round($sphtotal, 2).'</td>';
                                    echo '<td align="center">'.round($sph2total, 2).'</td>';
                                    echo '<td align="center">'.$callbacks.'</td>';
                                    echo '</tr>';
                                }

                                
                                    echo '<th>Total</th>';
                                    echo '<td align="center" id="calls"></td>';
                                    echo '<td align="center" id="leads"></td>';
                                    echo '<td align="center" id="completed"></td>';
                                    echo '<td align="center" id="contacts"></td>';
                                    echo '<td align="center" id="contactratio"></td>';
                                    echo '<td align="center" id="nonpausetime"></td>';
                                    echo '<td align="center" id="systemtime"></td>';
                                    echo '<td align="center" id="talktime"></td>';
                                    echo '<td align="center" id="sales"></td>';
                                    echo '<td align="center" id="sph1"></td>';
                                    echo '<td align="center" id="sph2"></td>';
                                    echo '<td align="center" id="callbacks"></td>';


                                  



                        echo '</tbody>';
             }
             
        }
    
        
    ?>
        

   </table>                 
<script>
var sum1 = 0;
var sum2 = 0;
var sum3 = 0;
var sum4 = 0;
var sum5 = 0;
var sum6 = 0;
var sum7 = 0;
var sum8 = 0;
var sum9 = 0;
var sum10= 0;
var sum11 = 0;
var sum12 = 0;
$("#table tr").not(':first').not(':last').each(function() {
  sum1 +=  getnum($(this).find("td:eq(1)").text());
  sum2 +=  getnum($(this).find("td:eq(2)").text());
  sum3 +=  getnum($(this).find("td:eq(3)").text());
  sum4 +=  getnum($(this).find("td:eq(4)").text());
  sum5 +=  getnum($(this).find("td:eq(5)").text());
  sum6 =  $(this).find("td:eq(6)").text();
  sum7 +=  getnum($(this).find("td:eq(7)").text());
  sum8 +=  getnum($(this).find("td:eq(8)").text());
  sum9 +=  getnum($(this).find("td:eq(9)").text());
  sum10 +=  getnum($(this).find("td:eq(10)").text());
  sum11 +=  getnum($(this).find("td:eq(11)").text());
  sum12 +=  getnum($(this).find("td:eq(12)").text());

  
  function getnum(t){ 
  	if(isNumeric(t)){
    	
        return parseFloat(t);
        
    }
    return 0;
	 	function isNumeric(n) {
  		return !isNaN(parseFloat(n)) && isFinite(n);
		}
  }

  
//get time



 
});


var sumsph1 = sum10.toFixed(2);
var sumsph2 = sum11.toFixed(2);

$("#calls").text(sum1);
$("#leads").text(sum2);
$("#completed").text(sum3);
$("#contacts").text(sum4);
$("#contactratio").text(sum5);
$("#nonpausetime").text(sum6);
$("#systemtime").text(sum7);
$("#talktime").text(sum8);
$("#sales").text(sum9);
$("#sph1").text(sumsph1);
$("#sph2").text(sumsph2);
$("#callbacks").text(sum12);
</script>
    </body>
</html>