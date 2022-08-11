<?php

////////////////////////////////////////////////////////////////
//                      Global variables                      //
////////////////////////////////////////////////////////////////

date_default_timezone_set('Europe/Madrid');
/* Echoclears the date
    h : 12 hr format
    H : 24 hr format
    i : Minutes
    s : Seconds
    u : Microseconds
    a : Lowercase am or pm
    l : Full text for the day
    F : Full text for the month
    j : Day of the month
    M : Month in letters
    m : month in numbers
    S : Sufix of the day st, nd, rd, etc
    Y : 4 digit year
*/

////////////////////////////////////////////////////////////////
//                         Functions                          //
////////////////////////////////////////////////////////////////
function processGateRealmRequest($action){
    $action_ix = 0;
    $result = [];
    while ($action[$action_ix]){
        switch($action[$action_ix]){
        case "open":
            // TODO: Execute command to open gate.
            $result[$action_ix] = ['result'=>'OK','realm'=>'gate','action'=>$action[$action_ix]];
            break;
        case 'close':
            // TODO: Execute command to close gate.
            $result[$action_ix] = ['result'=>'OK','realm'=>'gate','action'=>$action[$action_ix]];
            break;
        case 'get_state':
            // TODO: Execute command to get gate state.
            $result[$action_ix] = ['result'=>'TODO','realm'=>'gate','action'=>$action[$action_ix]];
            break;
        default:
            $result[$action_ix] = ['result'=>'NOK','realm'=>'gate','cause'=>'Action not recognized'];
            break;
        }
        $action_ix++;
    }
    return $result;
}

function processGardenRealmRequest($action){
    $action_ix = 0;
    $result = [];
    while ($action[$action_ix]){
        switch($action[$action_ix]){
        case 'lights_on':
            // TODO: Execute command to turn garden lights on.
            $result[$action_ix] = ['result'=>'OK','realm'=>'garden','action'=>$action[$action_ix]];
            break;
        case 'lights_off':
            // TODO: Execute command to turn garden lights off.
            $result[$action_ix] = ['result'=>'OK','realm'=>'garden','action'=>$action[$action_ix]];
            break;
        case 'get_state':
            // TODO: Execute command to get garden lights state.
            $result[$action_ix] = ['result'=>'TODO','realm'=>'garden','action'=>$action[$action_ix]];
            break;
        default:
            $result[$action_ix] = ['result'=>'NOK','realm'=>'garden','cause'=>'Action not recognized'];
            break;
        }
        $action_ix++;
    }
    return $result;
}

function processPoolRealmRequest($action){
    $action_ix = 0;
    $result = [];
    while ($action[$action_ix]){
        switch($action[$action_ix]){
        case 'action1':
            // TODO: Execute command to action1.
            $result[$action_ix] = ['result'=>'OK','realm'=>'pool','action'=>$action[$action_ix]];
            break;
        case 'action2':
            // TODO: Execute command to action2..
            $result[$action_ix] = ['result'=>'OK','realm'=>'pool','action'=>$action[$action_ix]];
            break;
        default:
            $result[$action_ix] = ['result'=>'NOK','realm'=>'pool','cause'=>'Action not recognized'];
            break;
        }
        $action_ix++;
    }
    return $result;
}

function processRequest($authorizedUsers, $user, $realm, $action){
    // Check if it is an authorized user
    if(!in_array($user, $authorizedUsers)){
        return array('NOK'=>'Unauthorized user');
    }

    // Process actions by realm.
    switch($realm){
    case 'gate':
        return processGateRealmRequest($action);
        break;
    case 'garden':
        return processGardenRealmRequest($action);
        break;
    case 'pool':
        return processPoolRealmRequest($action);
        break;
    default:
        return array('NOK'=>'Unrecognized realm');
        break;
    }
}

function processReqJson($req_json){
    /**
     * The request shall contain three pieces of data:
     * - user: String that identifies the user issuing the request.
     * - realm: String that determines the subject of the action.
     * - action: Action to be performed.
     * One request can have only one user and realm, but can carry many subject actions.
     */
    // Users declaration
    $systemUsers = ['Gate', 'Garden', 'Pool'];
    $authorizedUsers = ['Dudu', 'Rafa', 'Júúúju', 'Carminha'];

    $res_json = [];
    $json_ix = 0;
    while ($req_json[$json_ix] != null){
        // Check if the request is valid.
        if (($req_json->user == null) or ($req_json->realm == null) or ($req_json->action == null)){
            $res_json = ['result'=>'NOK'];
            $res_json += ['cause'=>'Bad request'];
            echo json_encode($res_json);
            exit();
        }

        // Get request data.
        $user   = $req_json[$json_ix]->user;
        $realm  = $req_json[$json_ix]->realm;
        $action = $req_json[$json_ix]->action;

        // Process request
        $res_json += processRequest($authorizedUsers, $user, $realm, $action);

        // Increment json indexer
        $json_ix++;
    }

    return $res_json;
}

////////////////////////////////////////////////////////////////
//                           Logic                            //
////////////////////////////////////////////////////////////////
// Take raw request data
$req = file_get_contents('php://input');

// Parse request data to JSON php object
$req_json = json_decode($req);

processReqJson($req_json);

// Send the response data
//$array_to_string = implode(", ", $data);
//error_log("DUDU_ERROR: ".$array_to_string,0);
//error_log("DUDU_ERROR: ".var_dump($req_json),0);
//var_dump($req_json);
echo json_encode($res_json);
?>
