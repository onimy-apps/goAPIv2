<?php
    #######################################################
    #### Name: goGetUserInfo.php	               ####
    #### Description: API to get specific user	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian Samatra        ####
    ####             Demian Lizandro Biscocho          ####
    #### License: AGPLv2                               ####
    #######################################################
        
    include_once("../goFunctions.php");

    ### POST or GET Variables
    $user_id = $_REQUEST['user_id'];
    $user_role = $_REQUEST['user_role'];
    //$user_id = "1359";
        
    ### Check user_id if its null or empty
    if($user_id == null) { 
            $apiresults = array("result" => "Error: Set a value for User ID."); 
    } else {
            $groupId = go_get_groupid($goUser);
            
            
    if (!checkIfTenant($groupId)) {
            $ul = "AND vicidial_users.user_id='$user_id'";
    } else { 
            $ul = "AND vicidial_users.user_id='$user_id' AND vicidial_users.user_group='$groupId'";  
    }
    
        $notAdminSQL = "AND vicidial_live_agents.user_level != '9'";
        $query_OnlineAgents = "SELECT count(*) as 'OnlineAgents' from vicidial_live_agents WHERE vicidial_live_agents.user_level != 4";
        $query_ParkedChannels = "SELECT channel as 'pc_channel',server_ip as 'pc_server_ip',channel_group as 'pc_channel_group',extension as 'pc_extension',parked_by as 'pc_parked_by',parked_time as 'pc_parked_time' from parked_channels limit 1";
        $query_CallerIDsFromVAC = "SELECT callerid as 'vac_callerid',lead_id as 'vac_lead_id,phone_number as 'vac_phone_number' from vicidial_auto_calls limit 1";
        $query_OnlineAgentsNoCalls = "SELECT vicidial_live_agents.extension as 'vla_extension',vicidial_live_agents.user as 'vla_user',vicidial_users.full_name as 'vu_full_name',vicidial_users.user_group as 'vu_user_group',vicidial_users.phone_login as 'vu_phone_login',vicidial_live_agents.conf_exten as 'vla_conf_exten',vicidial_live_agents.status as 'vla_status',vicidial_live_agents.comments as 'vla_comments',vicidial_live_agents.server_ip as 'vla_server_ip',vicidial_live_agents.call_server_ip as 'vla_call_server_ip',UNIX_TIMESTAMP(last_call_time) as 'last_call_time',UNIX_TIMESTAMP(last_call_finish) as last_call_finish,vicidial_live_agents.campaign_id as 'vla_campaign_id',UNIX_TIMESTAMP(last_state_change) as 'last_state_change',vicidial_live_agents.lead_id as 'vla_lead_id',vicidial_live_agents.agent_log_id as 'vla_agent_log_id',vicidial_users.user_id as 'vu_user_id',vicidial_live_agents.callerid as 'vla_callerid' FROM vicidial_live_agents,vicidial_users WHERE vicidial_live_agents.user=vicidial_users.user AND lead_id = 0 AND vicidial_live_agents.user_level != 4 $ul $notAdminSQL";
        $query_OnlineAgentsInCalls = "SELECT vicidial_live_agents.extension as 'vla_extension',vicidial_live_agents.user as 'vla_user',vicidial_users.full_name as 'vu_full_name',vicidial_users.user_group as 'vu_user_group',vicidial_users.phone_login as 'vu_phone_login',vicidial_live_agents.conf_exten as 'vla_conf_exten',vicidial_live_agents.status as 'vla_status',vicidial_live_agents.comments as 'vla_comments',vicidial_live_agents.server_ip as 'vla_server_ip',vicidial_live_agents.call_server_ip as 'vla_call_server_ip',UNIX_TIMESTAMP(last_call_time) as 'last_call_time',UNIX_TIMESTAMP(last_call_finish) as last_call_finish,vicidial_live_agents.campaign_id as 'vla_campaign_id',UNIX_TIMESTAMP(last_state_change) as 'last_state_change',vicidial_live_agents.lead_id as 'vla_lead_id',vicidial_live_agents.agent_log_id as 'vla_agent_log_id',vicidial_users.user_id as 'vu_user_id',vicidial_live_agents.callerid as 'vla_callerid',vicidial_list.phone_number as vl_phone_number FROM vicidial_live_agents,vicidial_users,vicidial_list WHERE vicidial_live_agents.user=vicidial_users.user AND vicidial_list.lead_id = vicidial_live_agents.lead_id AND vicidial_live_agents.user_level != 4  $ul $notAdminSQL";
        $query_GetUserInfo = "SELECT user_id, user, full_name, email, user_group, active, user_level, phone_login, phone_pass, voicemail_id, hotkeys_active FROM vicidial_users WHERE user_id = '$user_id';";
        
        //echo $query_OnlineAgentsInCalls;
        
        $rsltvInCalls = mysqli_query($link,$query_OnlineAgentsInCalls);
        $rsltvNoCalls = mysqli_query($link,$query_OnlineAgentsNoCalls);
        $rsltvParkedChannels = mysqli_query($link,$query_ParkedChannels);
        $rsltvCallerIDsFromVAC = mysqli_query($link,$query_CallerIDsFromVAC);
        $rsltvUserInfo = mysqli_query($link, $query_GetUserInfo);
        
        //$countrsltvInCalls = mysqli_num_rows($rsltvInCalls);
        //$countrsltvNoCalls = mysqli_num_rows($rsltvNoCalls);
        
        /* Declaration of Arrays */
                $dataInCalls = array();
                $dataNoCalls = array();
                $dataParkedChannels = array();
                $dataCallerIDsFromVAC = array();
                $userInfo = array();
        
        
        if($query_OnlineAgents != NULL) {
                while($resultsInCalls = mysqli_fetch_array($rsltvInCalls, MYSQLI_ASSOC)){               
                    array_push($dataInCalls, $resultsInCalls);
                }
                //echo "pre";
                //print_r($dataInCalls);
           
                while($resultsNoCalls = mysqli_fetch_array($rsltvNoCalls, MYSQLI_ASSOC)){               
                    array_push($dataNoCalls, $resultsNoCalls);
                }
                //echo "pre";
                //print_r($dataNoCalls);
            
                while($resultsParkedChannels = mysqli_fetch_array($rsltvParkedChannels, MYSQLI_ASSOC)){               
                    array_push($dataParkedChannels, $resultsParkedChannels);
                }
            
                while($resultsCallerIDsFromVAC = mysqli_fetch_array($rsltvCallerIDsFromVAC, MYSQLI_ASSOC)){               
                    array_push($dataCallerIDsFromVAC, $resultsCallerIDsFromVAC);
                }
        }
        
        if($query_GetUserInfo != NULL){
                while($resultsUserInfo = mysqli_fetch_array($rsltvUserInfo, MYSQLI_ASSOC)){               
                    array_push($userInfo, $resultsUserInfo);
                }
        }
        
        $data = array_merge($dataInCalls, $dataNoCalls, $dataParkedChannels, $dataCallerIDsFromVAC, $userInfo);
        //$data = array_unique($data); //removes duplicate in array
        
        if($data != NULL){
                $apiresults = array("result" => "success", "data" => $data); 
        }else{
                $apiresults = array("result" => "failed", "Error: Failed to get data. Please contact the administrator to fix the problem.", "query is: " => $query_GetUserInfo);
        }
       
    }
  

?>
