<?php
//require_once "../vendor/autoload.php";



require "Slim/Slim.php";
 
// create new Slim instance
$app = new Slim();

require "NotORM.php";
 

$dsn = "mysql:dbname=ecoman_18_08;host=localhost";
$username = "root";
$password = "";

//$pdo = new PDO($dsn, $username, $password);
/**
 *  zeynel dağlı
 * postgre test için 
 */
//$pdo = new PDO('pgsql:dbname=ecoman_18_08;host=localhost;user=postgres;password=1q2w3e4r');
//$db = new NotORM($pdo);

/**
 *  zeynel dağlı
 *  ecoman sunucusuna bağlanmak için 
 */
$pdo = new PDO('pgsql:dbname=ecoman_01_10;host=88.249.18.205;user=postgres;password=1q2w3e4r');
$db = new NotORM($pdo);


 
$app = new Slim(array(
    "MODE" => "development",
    "TEMPLATES.PATH" => "./templates"
));

$app->get("/", function() {
    echo "<h1>Hello Slim World</h1>";
});


/**
 *  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //$pdo->exec('SET NAMES "utf8"');
    
    $stmt=$pdo->prepare("SELECT
                        *
                        FROM companies 
                        ".$sort." ".$order." LIMIT ".$offset.",".$limit." ");
    // bu satır sonradan eklendi
    //$res = $db->query(  "SELECT * FROM t_clstr"  )->fetchAll(PDO::FETCH_ASSOC);
    try { 
            //$db->beginTransaction();
            //print_r($stmt);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $arraydata=array();
            while($r = $stmt->fetch()){
                $arraydata[]=$r;
            }
            $pdo=null;

            } catch(PDOException $e) { 
                $pdo->rollback(); 
            } 
 */
/**
 * zeynel dağlı
 * IS scoping testlerinde kullanılmak üzere test amaçlı yazılmıştır
 * @since 10-09-2014
 */
$app->get("/companies_", function () use ($app, $db) {
    $companies = array();
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']>0) {
             $offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
             $limit = intval($_GET['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = trim($_GET['sort']);
    } else {
        $sort = "id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }

    $count = $db->companies()->count('*');
    foreach ($db->companies()->limit($limit,$offset)->order(" ".$sort." ".$order." ") as $company) {
        $companies[]  = array(
            "id" => $company["id"],
            "company" => $company["company"],
            "woods" => $company["woods"],
            "woodcips" => $company["woodcips"],
            "metals" => $company["metals"],
            "solvents" => $company["solvents"],
            "acetone" => $company["acetone"],
            "ethanol" => $company["ethanol"]
        );
    }
    $app->response()->header("Content-Type", "application/json");
    //echo json_encode($companies);
    $resultArray = array();
    $resultArray['total'] = $count;
    $resultArray['rows'] = $companies;
    echo json_encode($resultArray);
});

/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/companydetails", function () use ($app, $db) {
    $companies = array();
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']>0) {
             $offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
             $limit = intval($_GET['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }
    } else {
        $limit = 10;
        $offset = 0;
    }


    $count = $db->companies()->count('*');
    foreach ($db->companies()->limit($limit,$offset) as $company) {
        $companies[]  = array(
            "id" => $company["id"],
            "company" => $company["company"],
            "wood" => $company["wood"],
            "woodcips" => $company["woodcips"],
            "metals" => $company["metals"],
            "solvents" => $company["solvents"],
            "acetone" => $company["acetone"],
            "ethanol" => $company["ethanol"]
        );
    }
    $app->response()->header("Content-Type", "application/json");
    //echo json_encode($companies);
    $resultArray = array();
    $resultArray['total'] = $count;
    $resultArray['rows'] = $companies;
    echo json_encode($resultArray);
});

/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/flows", function () use ($app, $db) {
    $flows = array();
    if(isset($_GET['id']) && $_GET['id']!="" ) {
        //$offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
        $flowFamilyID = intval($_GET['id']);
        
        foreach ($db->t_flow()->where("flow_family_id=? AND active=? ", $flowFamilyID, 1) as $flow) {
            $flows[]  = array(
                "id" => $flow["id"],
                "text" => $flow["name"],
                "checked" => true,
                "attributes" => array ("notroot"=>true)
            );
        }
        
        $app->response()->header("Content-Type", "application/json");
        //echo json_encode($companies);
        //$resultArray = array();
        //$resultArray['total'] = $count;
        //$resultArray['rows'] = $companies;
        echo json_encode($flows);
    
    } else {
        foreach ($db->t_flow_family() as $flow) {
            $flows[]  = array(
                "id" => $flow["id"],
                "text" => $flow["name"],
                "state" => 'closed',
                "checked" => true,
                "attributes" => array ("notroot"=>false)
            );
        }
        
        $app->response()->header("Content-Type", "application/json");
        //echo json_encode($companies);
        //$resultArray = array();
        //$resultArray['total'] = $count;
        //$resultArray['rows'] = $companies;
        echo json_encode($flows);
    }
     
});

/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/flowsManual", function () use ($app, $db) {
    $flows = array();
    if(isset($_GET['id']) && $_GET['id']!="" ) {
        //$offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
        $flowFamilyID = intval($_GET['id']);
        
        foreach ($db->t_flow()->where("flow_family_id=? && active=? ", $flowFamilyID, 1) as $flow) {
            $flows[]  = array(
                "id" => $flow["id"],
                "text" => $flow["name"]
                
            );
        }
        
        $app->response()->header("Content-Type", "application/json");
        //echo json_encode($companies);
        //$resultArray = array();
        //$resultArray['total'] = $count;
        //$resultArray['rows'] = $companies;
        echo json_encode($flows);
    
    } else {
        foreach ($db->t_flow_family() as $flow) {
            $flows[]  = array(
                "id" => $flow["id"],
                "text" => $flow["name"],
                "state" => 'closed',
                
            );
        }
        
        $app->response()->header("Content-Type", "application/json");
        //echo json_encode($companies);
        //$resultArray = array();
        //$resultArray['total'] = $count;
        //$resultArray['rows'] = $companies;
        echo json_encode($flows);
    }
     
});


/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/columnflows", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    $res = $pdo->query(  "SELECT * FROM t_flow;"  )->fetchAll(PDO::FETCH_ASSOC);
    //$res2 = $pdo->query(  "SELECT * FROM t_flow_family;"  )->fetchAll(PDO::FETCH_ASSOC);
    $columnArray = array();
    $dataArray = array();
    $dataArray[] =array('field'=>'company', 'title'=>'Company', 'width'=> 150);
    /*foreach ($res2 as $r){
        $columnArray['field'] = strtolower($r['name']);
        $columnArray['title'] = $r['name'];
        //$columnArray['field'] = 'test';
        //$columnArray['title'] = 'test';
        $columnArray['width'] = 80;
        $columnArray['sortable'] = true;
        $dataArray[] = $columnArray;
    }*/
    
    foreach ($res as $r){
        $columnArray['field'] = strtolower($r['name']);
        $columnArray['title'] = $r['name'];
        //$columnArray['field'] = 'test';
        //$columnArray['title'] = 'test';
        $columnArray['width'] = 80;
        $columnArray['sortable'] = true;
        $dataArray[] = $columnArray;
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    //$pdo=null;
    $app->response()->header("Content-Type", "application/json");
    echo json_encode($dataArray);
});



/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/companies", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']==0){ $pageNum = 1; } else { $pageNum=intval($_GET['page']); };
        $offset = ((intval($pageNum)-1)* intval($_GET['rows']));
        
        //$offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
        //$offset = ($pageNum * intval($_GET['rows']));
        
        $limit = intval($_GET['rows']);
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = ucfirst(trim($_GET['sort']));
    } else {
        $sort = "id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    
    
   /* echo 'select distinct 

                            a.cmpny_id as id,
                            cc.name as company

                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 1 ) as "Water"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 2 )  as "Electricity"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 3 )  as "Aliminium"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 4 )  as "Brass"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 5 )  as "Copper"    
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id=6 )  as "Lead"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id=7 )  as "Zinc"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 8 )  as "Acetone"     
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 9) as "Ketone" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 10) as "Acetoin" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 11) as "Ethanol" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 12) as "Peroxide" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 13) as "Woodcips"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 14) as "Cellulose" 

                            from  sm_cmpny_flow_quantity  a 
                            inner join  t_cmpny cc on cc.id = a.cmpny_id
                            ORDER BY  "'.$sort.'" '.$order.' LIMIT '.$limit.' OFFSET '.$offset.' ';*/
    
    
    $res = $pdo->query('select distinct 

                            a.cmpny_id as id,
                            cc.name as company

                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 1 ) as "Water"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 2 )  as "Electricity"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 3 )  as "Aliminium"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 4 )  as "Brass"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 5 )  as "Copper"    
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id=6 )  as "Lead"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id=7 )  as "Zinc"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 8 )  as "Acetone"     
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 9) as "Ketone" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 10) as "Acetoin" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 11) as "Ethanol" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 12) as "Peroxide" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 13) as "Woodcips"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 14) as "Cellulose" 

                            from  sm_cmpny_flow_quantity  a 
                            inner join  t_cmpny cc on cc.id = a.cmpny_id
                            ORDER BY  "'.$sort.'" '.$order.' LIMIT '.$limit.' OFFSET '.$offset.'  ')->fetchAll(PDO::FETCH_ASSOC);
                            //ORDER BY  ".$order." ".$sort." LIMIT ".$offset.",".$limit."
    $res2 = $pdo->query(  "select count(*) as toplam from t_cmpny 
                            WHERE id IN (SELECT DISTINCT cmpny_id from sm_cmpny_flow_quantity );"  )->fetchAll(PDO::FETCH_ASSOC);
    $companies = array();
    foreach ($res as $company){
        $companies[]  = array(
            "id" => $company["id"],
            "company" => $company["company"],
            //"woods" => $company["Wood"],
            "woodcips" => $company["Woodcips"],
            //"metals" => $company["Metals"],
            //"solvents" => $company["Solvents"],
            "acetone" => $company["Acetone"],
            "acetoin" => $company["Acetoin"],
            "ketone" => $company["Ketone"],
            "ethanol" => $company["Ethanol"],
            "peroxide" => $company["Peroxide"],
            "cellulose" => $company["Cellulose"],
            "zinc" => $company["Zinc"],
            "lead" => $company["Lead"],
            "copper" => $company["Copper"],
            "brass" => $company["Brass"],
            "aliminium" => $company["Aliminium"],
            "electricity" => $company["Electricity"],
            "water" => $company["Water"]
        );
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    $app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = $res2[0]['toplam'];
    $resultArray['rows'] = $companies;
    echo json_encode($resultArray);
    
});


/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/companyFlows", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    if(isset($_GET['companyid']) && $_GET['companyid']!="" ) {
        $companyID = intval($_GET['companyid']);
    } 
    
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']>0) {
             $offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
             $limit = intval($_GET['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = trim($_GET['sort']);
    } else {
        $sort = "id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    
    $res = $pdo->query("SELECT 
                            sm.flow_id as id,
                            f.name as flow,
                            sm.qntty as qntty,
                            u.name as unit,
                            sm.quality as quality,
                            ft.name as flowtype
                            FROM sm_cmpny_flow_quantity sm
                            LEFT JOIN t_flow f on sm.flow_id = f.id
                            LEFT JOIN t_unit u on sm.unit_id = u.id
                            LEFT JOIN t_flow_type ft  on sm.flow_type_id = ft.id
                            WHERE cmpny_id=".$companyID."
                            ORDER BY  ".$sort." ".$order." LIMIT ".$limit." OFFSET ".$offset."  ")->fetchAll(PDO::FETCH_ASSOC);
                            //ORDER BY  ".$order." ".$sort." LIMIT ".$offset.",".$limit."
    $res2 = $pdo->query(  "SELECT 
                            count(*) as toplam
                            FROM sm_cmpny_flow_quantity

                            WHERE cmpny_id=".$companyID.";"  )->fetchAll(PDO::FETCH_ASSOC);
    $flows = array();
    foreach ($res as $flow){
        $flows[]  = array(
            "id" => $flow["id"],
            "flow" => $flow["flow"],
            "qntty" => $flow["qntty"],
            "unit" => $flow["unit"],
            "quality" => $flow["quality"],
            "flowtype" => $flow["flowtype"],
        );
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    $app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = $res2[0]['toplam'];
    $resultArray['rows'] = $flows;
    //json_decode($_GET["selectedFlows"]);
    
    echo json_encode($resultArray);
    
});


/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/flowCompanies", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    if(isset($_GET['flowid']) && $_GET['flowid']!="" ) {
        $flowID = intval($_GET['flowid']);
    } 
    
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']>0) {
             $offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
             $limit = intval($_GET['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = trim($_GET['sort']);
    } else {
        $sort = "id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    
    $res = $pdo->query("SELECT 
                            sm.cmpny_id as id,
                            cm.name as company,
                            sm.qntty as qntty,
                            u.name as unit,
                            sm.quality as quality,
                            ft.name as flowtype
                            FROM sm_cmpny_flow_quantity sm
                            LEFT JOIN t_flow f on sm.flow_id = f.id
                            LEFT JOIN t_unit u on sm.unit_id = u.id
                            LEFT JOIN t_flow_type ft  on sm.flow_type_id = ft.id
                            LEFT JOIN t_cmpny cm  on sm.cmpny_id= cm.id
                            WHERE flow_id=".$flowID."
                            ORDER BY  ".$sort." ".$order." LIMIT ".$limit." OFFSET ".$offset."  ")->fetchAll(PDO::FETCH_ASSOC);
                            //ORDER BY  ".$order." ".$sort." LIMIT ".$offset.",".$limit."
    $res2 = $pdo->query(  "SELECT 
                            count(*) as toplam
                            FROM sm_cmpny_flow_quantity

                            WHERE flow_id=".$flowID.";"  )->fetchAll(PDO::FETCH_ASSOC);
    $flows = array();
    foreach ($res as $flow){
        $flows[]  = array(
            "id" => $flow["id"],
            "company" => $flow["company"],
            "qntty" => $flow["qntty"],
            "unit" => $flow["unit"],
            "quality" => $flow["quality"],
            "flowtype" => $flow["flowtype"],
        );
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    $app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = $res2[0]['toplam'];
    $resultArray['rows'] = $flows;
    echo json_encode($resultArray);
    
});


/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/flowsAndCompanies", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    if(isset($_GET['flows']) && $_GET['flows']!="" ) {
        $flows = json_decode($_GET['flows'], true);
        //print_r($flows);
        $flowsStr="";
        foreach ($flows as $key=>$value){
            $flowsStr.= $value.',';
        }
        $flowsStr = rtrim($flowsStr, ',');
    } 
    //echo $flowsStr;
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']>0) {
             $offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
             $limit = intval($_GET['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = trim($_GET['sort']);
    } else {
        $sort = "id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    
    $res = $pdo->query("SELECT 
                            sm.cmpny_id as id,
                            cm.name as company,
                            f.name as flow,
                            sm.qntty as qntty,
                            u.name as unit,
                            sm.quality as quality,
                            ft.name as flowtype
                            FROM sm_cmpny_flow_quantity sm
                            LEFT JOIN t_flow f on sm.flow_id = f.id
                            LEFT JOIN t_unit u on sm.unit_id = u.id
                            LEFT JOIN t_flow_type ft  on sm.flow_type_id = ft.id
                            LEFT JOIN t_cmpny cm  on sm.cmpny_id= cm.id
                            WHERE flow_id IN (".$flowsStr.")
                            ORDER BY  ".$sort." ".$order." LIMIT ".$limit." OFFSET ".$offset."  ")->fetchAll(PDO::FETCH_ASSOC);
                            //ORDER BY  ".$order." ".$sort." LIMIT ".$offset.",".$limit."
    $res2 = $pdo->query(  "SELECT 
                            count(*) as toplam
                            FROM sm_cmpny_flow_quantity

                            WHERE flow_id IN (".$flowsStr.");"  )->fetchAll(PDO::FETCH_ASSOC);
    $flows = array();
    foreach ($res as $flow){
        $flows[]  = array(
            "id" => $flow["id"],
            "company" => $flow["company"],
            "qntty" => $flow["qntty"],
            "unit" => $flow["unit"],
            "quality" => $flow["quality"],
            "flowtype" => $flow["flowtype"],
            "flow" => $flow["flow"],
        );
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    //$app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = $res2[0]['toplam'];
    $resultArray['rows'] = $flows;
    echo json_encode($resultArray);
    
});



/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/ISPotentials", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']>0) {
             $offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
             $limit = intval($_GET['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = trim($_GET['sort']);
    } else {
        $sort = "id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    
    if(isset($_GET["selectedFlows"]) && $_GET["selectedFlows"]!=null && isset($_GET["companies"]) && $_GET["companies"]!=null) {
        $flowStr = rtrim($_GET["selectedFlows"], ",");
         $companyStr = rtrim($_GET["companies"], ",");
  
        $res = $pdo->query('select  

                            a.cmpny_id as id , 
                            cc.name as company,
                            b.cmpny_id,
                            f.name as flow,
                            fta.name as fromflowtype,
                            dd.name as gittigifirma,
                            ftb.name as toflowtype
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 1 and a.flow_id = b.flow_id) as "Water"
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 1 and a.flow_id = b.flow_id) as "Water2"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 2 and a.flow_id = b.flow_id)  as "Electricity"
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 2 and a.flow_id = b.flow_id)  as "Electricity2"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 3 and a.flow_id = b.flow_id)  as "Aliminium"
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 3 and a.flow_id = b.flow_id)  as "Aliminium2"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 4 and a.flow_id = b.flow_id)  as "Brass"
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 4 and a.flow_id = b.flow_id)  as "Brass2"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 5 and a.flow_id = b.flow_id)  as "Copper" 
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 5 and a.flow_id = b.flow_id)  as "Copper2" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id=6 and a.flow_id =b.flow_id )  as "Lead"
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id=6 and a.flow_id =b.flow_id )  as "Lead2"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id=7 and a.flow_id = b.flow_id)  as "Zinc"
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id=7 and a.flow_id = b.flow_id)  as "Zinc2"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 8 and a.flow_id = b.flow_id)  as "Acetone" 
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 8 and a.flow_id = b.flow_id)  as "Acetone2" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 9 and a.flow_id = b.flow_id) as "Ketone"
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 9 and a.flow_id = b.flow_id) as "Ketone2"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 10 and a.flow_id = b.flow_id)  as "Acetoin" 
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 10 and a.flow_id = b.flow_id)  as "Acetoin2" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 11 and a.flow_id = b.flow_id) as "Ethanol" 
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 11 and a.flow_id = b.flow_id) as "Ethanol2" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 12 and a.flow_id = b.flow_id) as "Peroxide" 
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 12 and a.flow_id = b.flow_id) as "Peroxide2"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 13 and a.flow_id = b.flow_id) as "Woodcips"
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 13 and a.flow_id = b.flow_id) as "Woodcips2"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 14 and a.flow_id = b.flow_id) as "Cellulose" 
                           ,(select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 14 and a.flow_id = b.flow_id) as "Cellulose2" 


                          from  sm_cmpny_flow_quantity  a 
                          inner join sm_cmpny_flow_quantity b on b.flow_id = a.flow_id 
                          inner join  t_cmpny cc on cc.id = a.cmpny_id
                          inner join  t_cmpny dd on dd.id = b.cmpny_id
                          inner join  t_flow f on b.flow_id = f.id
                          inner join  t_flow_type fta on a.flow_type_id = fta.id
                          inner join  t_flow_type ftb on b.flow_type_id = ftb.id

                          where a.cmpny_id in ('.$companyStr.') and a.flow_id  in ('.$flowStr.') 
                          order by a.cmpny_id, b.flow_id ,cc.name   
                           ')->fetchAll(PDO::FETCH_ASSOC);
                            //LIMIT '.$limit.' OFFSET '.$offset.' 
    /*$res2 = $pdo->query(  "select count(*) as toplam from t_cmpny 
                            WHERE id IN (SELECT DISTINCT cmpny_id from sm_cmpny_flow_quantity );"  )->fetchAll(PDO::FETCH_ASSOC);*/
    $companies = array();
    foreach ($res as $company){
        
        if($company["Aliminium"]!= null) {
               $qntty = $company["Aliminium"];
            } else if($company["Acetone"]!=null) {
                $qntty = $company["Acetone"];
            }
            else if($company["Acetoin"]!=null) {
                $qntty = $company["Acetoin"];
            }
            else if($company["Ketone"]!=null) {
                $qntty = $company["Ketone"];
            } 
            else if($company["Ethanol"]!=null) {
                $qntty = $company["Ethanol"];
            } 
            else if($company["Peroxide"]!=null) {
                $qntty = $company["Peroxide"];
            } 
            else if($company["Cellulose"]!=null) {
                $qntty = $company["Cellulose"];
            } 
            else if($company["Zinc"]!=null) {
                $qntty = $company["Zinc"];
            } 
            else if($company["Lead"]!=null) {
                $qntty = $company["Lead"];
            } 
            else if($company["Copper"]!=null) {
                $qntty = $company["Copper"];
            } 
            else if($company["Brass"]!=null) {
                $qntty = $company["Brass"];
            }
            else if($company["Aliminium"]!=null) {
                $qntty = $company["Aliminium"];
            }
            else if($company["Electricity"]!=null) {
                $qntty = $company["Electricity"];
            }
            else if($company["Water"]!=null) {
                $qntty = $company["Water"];
            }
            else {
                $qntty = null;
            }
            
            
            if($company["Aliminium2"]!= null) {
               $qntty2 = $company["Aliminium2"];
            } else if($company["Acetone2"]!=null) {
                $qntty2 = $company["Acetone2"];
            }
            else if($company["Acetoin2"]!=null) {
                $qntty2 = $company["Acetoin2"];
            }
            else if($company["Ketone2"]!=null) {
                $qntty2 = $company["Ketone2"];
            } 
            else if($company["Ethanol2"]!=null) {
                $qntty2 = $company["Ethanol2"];
            } 
            else if($company["Peroxide2"]!=null) {
                $qntty2 = $company["Peroxide2"];
            } 
            else if($company["Cellulose2"]!=null) {
                $qntty2 = $company["Cellulose2"];
            } 
            else if($company["Zinc2"]!=null) {
                $qntty2 = $company["Zinc2"];
            } 
            else if($company["Lead2"]!=null) {
                $qntty2 = $company["Lead2"];
            } 
            else if($company["Copper2"]!=null) {
                $qntty2 = $company["Copper2"];
            } 
            else if($company["Brass2"]!=null) {
                $qntty2 = $company["Brass2"];
            }
            else if($company["Aliminium2"]!=null) {
                $qntty2 = $company["Aliminium2"];
            }
            else if($company["Electricity2"]!=null) {
                $qntty2 = $company["Electricity2"];
            }
            else if($company["Water2"]!=null) {
                $qntty2 = $company["Water2"];
            }
            else {
                $qntty2 = null;
            }
        
        
        $companies[]  = array(
            "id" => $company["id"],
            "company" => $company["company"],
            "tocompany" => $company["gittigifirma"],
            "fromflowtype" => $company["fromflowtype"],
            "toflowtype" => $company["toflowtype"],
            "flow" => $company["flow"],
            "qntty" => $qntty,
            "qntty2" => $qntty2,
            "woodcips" => $company["Woodcips"],
            "acetone" => $company["Acetone"],
            "acetoin" => $company["Acetoin"],
            "ketone" => $company["Ketone"],
            "ethanol" => $company["Ethanol"],
            "peroxide" => $company["Peroxide"],
            "cellulose" => $company["Cellulose"],
            "zinc" => $company["Zinc"],
            "lead" => $company["Lead"],
            "copper" => $company["Copper"],
            "brass" => $company["Brass"],
            "aliminium" => $company["Aliminium"],
            "electricity" => $company["Electricity"],
            "water" => $company["Water"]
        );
        $qntty=null;
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    $app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = count($res);
    $resultArray['rows'] = $companies;
    echo json_encode($resultArray);
    } else {
        $arraySonuc = array("notFound"=>true);
        echo json_encode($arraySonuc);
    }

});


/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/ISPotentialsNew", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']>0) {
             $offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
             $limit = intval($_GET['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = trim($_GET['sort']);
    } else {
        $sort = "id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    
    if(isset($_GET["selectedFlows"]) && $_GET["selectedFlows"]!=null && isset($_GET["companies"]) && $_GET["companies"]!=null) {
        $flowStr = rtrim($_GET["selectedFlows"], ",");
         $companyStr = rtrim($_GET["companies"], ",");
         
        $res = $pdo->query('select  

                            a.cmpny_id as id, 
                            cc.name as company,
                            dd.id as tocompanyid,
                            f.name as flow,
                            f.id as flowid,
                            fta.name as fromflowtype,
                            dd.name as gittigifirma,
                            ftb.name as toflowtype,
                            ua.name as qnttyunit,
                            ub.name as qntty2unit
			    ,CASE WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 3 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 3
			     and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 1 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 1 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 2 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 2 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 3 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 3 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 4 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 4 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 5 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 5 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 6 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 6 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 7 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 7 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 8 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 8 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 9 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 9 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 10 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 10 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 11 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 11 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 12 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 12 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 13 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 13 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 14 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 14 and a.flow_id = b.flow_id)
				    ELSE 1
			     END AS "qntty"
			     ,CASE WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 3 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 3
			     and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 1 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 1 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 2 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 2 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 3 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 3 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 4 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 4 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 5 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 5 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 6 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 6 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 7 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 7 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 8 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 8 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 9 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 9 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 10 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 10 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 11 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 11 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 12 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 12 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 13 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 13 and a.flow_id = b.flow_id)
				    WHEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 14 and a.flow_id = b.flow_id)>0 THEN (select qntty from sm_cmpny_flow_quantity a where  b.cmpny_id=a.cmpny_id and b.flow_id= 14 and a.flow_id = b.flow_id)
				    ELSE 1
			     END AS "qntty2"



                          from  sm_cmpny_flow_quantity  a 
                          inner join sm_cmpny_flow_quantity b on b.flow_id = a.flow_id 
                          inner join  t_cmpny cc on cc.id = a.cmpny_id
                          inner join  t_cmpny dd on dd.id = b.cmpny_id
                          inner join  t_flow f on b.flow_id = f.id
                          inner join  t_flow_type fta on a.flow_type_id = fta.id
                          inner join  t_flow_type ftb on b.flow_type_id = ftb.id
                          inner join  t_unit ua on a.unit_id = ua.id
                          inner join  t_unit ub on b.unit_id = ub.id

                          where a.cmpny_id in ('.$companyStr.') and a.flow_id  in ('.$flowStr.') 
                          order by a.cmpny_id, b.flow_id ,cc.name   
                           ')->fetchAll(PDO::FETCH_ASSOC);
                            //LIMIT '.$limit.' OFFSET '.$offset.' 
    /*$res2 = $pdo->query(  "select count(*) as toplam from t_cmpny 
                            WHERE id IN (SELECT DISTINCT cmpny_id from sm_cmpny_flow_quantity );"  )->fetchAll(PDO::FETCH_ASSOC);*/
    
    $companies = array();
    foreach ($res as $company){
        
        $companies[]  = array(
            "id" => $company["id"].",".$company["tocompanyid"].",".$company["flowid"],
            "company" => $company["company"],
            "tocompany" => $company["gittigifirma"],
            "fromflowtype" => $company["fromflowtype"],
            "toflowtype" => $company["toflowtype"],
            "flow" => $company["flow"],
            //"qntty" => $company["qntty"],
            "qntty" => $company["a_miktar"],
            //"qntty2" => $company["qntty2"],
            "qntty2" => $company["b_miktar"],
            "qntty2unit" => $company["qntty2unit"],
            "qnttyunit" => $company["qnttyunit"]
        );
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    $app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = count($res);
    $resultArray['rows'] = $companies;
    echo json_encode($resultArray);
    } else {
        $arraySonuc = array("notFound"=>true);
        echo json_encode($arraySonuc);
    }

});


/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->post("/insertIS", function () use ($app, $db, $pdo) {
    ////$pdo->exec('SET NAMES "utf8"');
    $stmt_log=$pdo->prepare("
        INSERT INTO t_is_prj(synergy_id, consultant_id,  name) VALUES (1,2,:name)");
    $test = $_POST["row"];
    
    /*
    INSERT INTO t_is_prj_details(
            cmpny_from_id, cmpny_to_id, flow_id, from_quantity, to_quantity, 
            is_prj_id)
    VALUES (?, ?, ?, ?, ?, 
            ?);
    */
    try { 
                $pdo->beginTransaction(); 
                
                $id =$stmt_log->execute(array(':name'=>$_POST["text"]));
                //echo json_encode(array("query1"=>$pdo->errorCode()));
                //echo json_encode(array("query1 info"=>$pdo->errorInfo()));
                $insertID = $pdo->lastInsertId("t_is_prj_id_seq");
                $arrayRows = json_decode(stripslashes($test), true);
                $insertValueStr="";
                foreach ($arrayRows as $row){
                        $arrExplode = explode(",", $row["id"]);
                        $insertValueStr.= "(".$arrExplode[0].",".$arrExplode[1].",".$arrExplode[2].",".$row["qntty1"].",".$row["qntty2"].",".$insertID."),";

                }
                $insertValueStr = rtrim($insertValueStr,",");
                //print_r($insertValueStr);
                /*print_r("INSERT INTO t_is_prj_details(
                                    cmpny_from_id, cmpny_to_id, flow_id, from_quantity, to_quantity, 
                                    is_prj_id)
                            VALUES ".$insertValueStr." ");*/
                $stmt_IS_detail=$pdo->prepare("INSERT INTO t_is_prj_details(
                                    cmpny_from_id, cmpny_to_id, flow_id, from_quantity, to_quantity, 
                                    is_prj_id)
                            VALUES ".$insertValueStr." ");
                $res = $stmt_IS_detail->execute();
                $pdo->commit();
                //$pdo->
                /*if($pdo->commit()){
                    echo json_encode(array("found"=>$pdo->errorCode()));
                } else {
                    echo json_encode(array("found"=>false));
                }*/
                echo json_encode(array("found"=>true));

            } catch(PDOException $e) { 
                $pdo->rollback(); 
                echo json_encode(array("notFound"=>true));
            } 
    
});







/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$json_str=null;
$app->get("/columnflows_json_test", function () use ($app, $db, $pdo, $json_str) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    $res = $pdo->query(  "SELECT * FROM t_flow;"  )->fetchAll(PDO::FETCH_ASSOC);
    //$res2 = $pdo->query(  "SELECT * FROM t_flow_family;"  )->fetchAll(PDO::FETCH_ASSOC);
    $columnArray = array();
    $dataArray = array();
    $testArray = array();
    $dataArray[] =array('field'=>'company', 'title'=>'Company', 'width'=> 150, 'sortable'=>true);
    /*foreach ($res2 as $r){
        $columnArray['field'] = strtolower($r['name']);
        $columnArray['title'] = $r['name'];
        //$columnArray['field'] = 'test';
        //$columnArray['title'] = 'test';
        $columnArray['width'] = 80;
        $columnArray['sortable'] = true;
        $dataArray[] = $columnArray;
    }*/
    //$json_str.='[';
    foreach ($res as $r){
        //$columnArray['field'] = strtolower($r['name']);
        $columnArray['field'] = trim($r['name']);
        $columnArray['title'] = $r['name'];
        //$columnArray['field'] = 'test';
        //$columnArray['title'] = 'test';
        $columnArray['width'] = 80;
        $columnArray['sortable'] = true;
        /*$columnArray['styler'] = "function(value,row,index){
				if (value < 20){
					return 'background-color:#ffee00;color:red;';
					// the function can return predefined css class and inline style
					// return {class:'c1',style:'color:red'}
				}
			}";*/
        //$columnArray['colspan'] = 1;
        
        
        /*
        $testArray['field'] = 'test';
        $testArray['title'] = 'test title';
        $testArray['width'] = 80;
        $testArray['sortable'] = true;

         */

        $dataArray[] = $columnArray;

        /*
        $json_str.= '['.json_encode($columnArray).'],';
        $json_str.= '['.json_encode($testArray).'],';

         */
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    //$pdo=null;
    $app->response()->header("Content-Type", "application/json");
    echo json_encode($dataArray);
    /*
    $json_str = rtrim($json_str, ',');
    $json_str.=']';
    echo $json_str;
     */
});


/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/companies_json_test", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']==0){ $pageNum = 1; } else { $pageNum=intval($_GET['page']); };
        $offset = ((intval($pageNum)-1)* intval($_GET['rows']));
        
        //$offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
        //$offset = ($pageNum * intval($_GET['rows']));
        
        $limit = intval($_GET['rows']);
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = ucfirst(trim($_GET['sort']));
    } else {
        $sort = "id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    
    if(isset($_GET['filterRules']) && $_GET['filterRules']!="") {
        $filterRules = trim($_GET['filterRules']);
        //print_r(json_decode($filterRules));
        $jsonFilter = json_decode($filterRules);
        //print_r($jsonFilter[0]->field);
        foreach ($jsonFilter as $std) {
            //print_r($std->field);
        }
        
    } else {
        $filterRules = "";
    }

    $res = $pdo->query('select distinct 

                            a.cmpny_id as id,
                            cc.name as company

                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 1 ) as "Water"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 2 )  as "Electricity"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 3 )  as "Aliminium"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 4 )  as "Brass"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 5 )  as "Copper"    
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id=6 )  as "Lead"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id=7 )  as "Zinc"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 8 )  as "Acetone"     
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 9) as "Ketone" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 10) as "Acetoin" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 11) as "Ethanol" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 12) as "Peroxide" 
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 13) as "Woodcips"
                           ,(select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id= 14) as "Cellulose" 

                            from  sm_cmpny_flow_quantity  a 
                            inner join  t_cmpny cc on cc.id = a.cmpny_id 
                            /*WHERE Zinc > 30*/
                            /*WHERE (select qntty from sm_cmpny_flow_quantity b where  b.cmpny_id=a.cmpny_id and b.flow_id=7 )>30*/
                            ORDER BY  "'.$sort.'" '.$order.' LIMIT '.$limit.' OFFSET '.$offset.'  ')->fetchAll(PDO::FETCH_ASSOC);
                            //ORDER BY  ".$order." ".$sort." LIMIT ".$offset.",".$limit."
    $res2 = $pdo->query(  "select count(*) as toplam from t_cmpny 
                            WHERE id IN (SELECT DISTINCT cmpny_id from sm_cmpny_flow_quantity );"  )->fetchAll(PDO::FETCH_ASSOC);
    $companies = array();
    foreach ($res as $company){
        $companies[]  = array(
            "id" => $company["id"],
            //"company" => "<a href=\"#\" title=\"This is the tooltip message.\" class=\"easyui-tooltip\">Hover me</a> ",
           "company" => "<a href=\"#\"  id='".$company["id"]."_1'  title=\"This is the tooltip message zeynel.\" class=\"easyui-tooltip\" data-options=\"
                \">".$company["company"]."</a>
                <script>$('#".$company["id"]."_1').tooltip({
                    position: 'right',
                    content: $('<div></div>'),
                    //content: '<span style=\"color:#fff\">This is the tooltip message.</span>',
                    onShow: function(){
                        /*$(this).tooltip('tip').css({
                            backgroundColor: '#666',
                            borderColor: '#666'
                        });*/
                        $(this).tooltip('arrow').css('top', 20);
                        $(this).tooltip('tip').css('top', $(this).offset().top);
                    },
                    onUpdate: function(cc){
                        cc.panel({
                            width: 500,
                            height: 'auto',
                            border: false,
                            href: 'tooltip_ajax.html'
                        });
                    }
                    
                });</script>    
                ",
            //"company" => "<a href='#'  >".$company["company"]."</a>",
            //"woods" => $company["Wood"],
            "woodcips" => $company["Woodcips"],
            "test" => "test deneme",
            //"metals" => $company["Metals"],
            //"solvents" => $company["Solvents"],
            "acetone" => $company["Acetone"],
            "acetoin" => $company["Acetoin"],
            "ketone" => $company["Ketone"],
            "ethanol" => $company["Ethanol"],
            "peroxide" => $company["Peroxide"],
            "cellulose" => $company["Cellulose"],
            "zinc" => $company["Zinc"],
            "lead" => $company["Lead"],
            "copper" => $company["Copper"],
            "brass" => $company["Brass"],
            /*"brass" => 
                    "<a href=\"#\"  id='".$company["id"]."_1_brass'  title=\"This is the tooltip message zeynel.\" class=\"easyui-tooltip\" data-options=\"
                \">".$company["Brass"]."</a>
                <script>$('#".$company["id"]."_1_brass').tooltip({
                    position: 'right',
                    content: $('<div></div>'),
                    //content: '<span style=\"color:#fff\">This is the tooltip message.</span>',
                    onShow: function(){
                        /*$(this).tooltip('tip').css({
                            backgroundColor: '#666',
                            borderColor: '#666'
                        });*/
                        /*$(this).tooltip('arrow').css('top', 20);
                        $(this).tooltip('tip').css('top', $(this).offset().top);
                    },
                    onUpdate: function(cc){
                        cc.panel({
                            width: 500,
                            height: 'auto',
                            border: false,
                            href: 'tooltip_ajax.html'
                        });
                    }
                    
                });</script>    
                ",*/
            "aliminium" => $company["Aliminium"],
            "electricity" => $company["Electricity"],
            "water" => $company["Water"]
        );
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    $app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = $res2[0]['toplam'];
    $resultArray['rows'] = $companies;
    echo json_encode($resultArray);
    
});


/**
 * zeynel dağlı
 * @since 24-11-2014
 */
$app->get("/companies_json_test2", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']==0){ $pageNum = 1; } else { $pageNum=intval($_GET['page']); };
        $offset = ((intval($pageNum)-1)* intval($_GET['rows']));
        
        //$offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
        //$offset = ($pageNum * intval($_GET['rows']));
        
        $limit = intval($_GET['rows']);
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        //$sort = ucfirst(trim($_GET['sort']));
        if(trim($_GET['sort']=='cmpny_name')) {
            $sort = "cmpny_name";
        } else {
             $sort = trim($_GET['sort']);
             $sort = ' CAST("'.trim($_GET['sort']).'"->\'flow_properties\'->>\'quantity\' AS numeric)  ';
        }
       
    } else {
        $sort = "cmpny_name";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    
    $sorguStr=null;
    if(isset($_GET['filterRules']) && $_GET['filterRules']!="") {
        $filterRules = trim($_GET['filterRules']);
        //print_r(json_decode($filterRules));
        $jsonFilter = json_decode($filterRules, true);
        //print_r($jsonFilter[0]->field);
        $sorguExpression = null;
        foreach ($jsonFilter as $std) {
            switch (trim($std['op'])) {
                case 'greater':
                    $sorguExpression = '>';
                    $sorguStr.=' CAST("'.$std['field'].'"->\'flow_properties\'->>\'quantity\' AS numeric)  '.$sorguExpression.''.$std['value'].' AND ';
                    break;
                case 'equal':
                    $sorguExpression = '=';
                    $sorguStr.=' CAST("'.$std['field'].'"->\'flow_properties\'->>\'quantity\' AS numeric)  '.$sorguExpression.''.$std['value'].' AND ';
                    break;
                case 'notequal':
                    $sorguExpression = '<>';
                    $sorguStr.=' CAST("'.$std['field'].'"->\'flow_properties\'->>\'quantity\' AS numeric)  '.$sorguExpression.''.$std['value'].' AND ';
                    break;
                case 'less':
                    $sorguExpression = '<';
                    $sorguStr.=' CAST("'.$std['field'].'"->\'flow_properties\'->>\'quantity\' AS numeric)  '.$sorguExpression.''.$std['value'].' AND ';
                    break;
                case 'contains':
                    $sorguExpression = 'LIKE';
                    $sorguStr.=' cmp.name ILIKE \'%'.$std['value'].'%\' AND ';
                    continue;
                    break;
                default:
                    $sorguExpression = '=';
                    $sorguStr.=' CAST("'.$std['field'].'"->\'flow_properties\'->>\'quantity\' AS numeric)  '.$sorguExpression.''.$std['value'].' AND ';
                    break;
            }
            //print_r($std->field);
            //print_r($std);
            
        }
        $sorguStr = rtrim($sorguStr,"AND ");
        if($sorguStr!="") $sorguStr = "WHERE ".$sorguStr;
        
        
    } else {
        $sorguStr=null;
        $filterRules = "";
    }

    /*print_r('SELECT * FROM t_flow_total_per_cmpny
                            INNER JOIN t_cmpny as cmp ON cmp.id = t_flow_total_per_cmpny.cmpny_id
                            '.$sorguStr.'
                            ORDER BY  '.$sort.' '.$order.' LIMIT '.$limit.' OFFSET '.$offset.'
                        ;');*/
    $res = $pdo->query('SELECT * FROM t_flow_total_per_cmpny
                            INNER JOIN t_cmpny as cmp ON cmp.id = t_flow_total_per_cmpny.cmpny_id
                            '.$sorguStr.'
                            /*WHERE CAST("Brass"->\'flow_properties\'->>\'quantity\' AS integer) >20*/
                            /*AND CAST("Brass"->\'flow_properties\'->>\'quantity\' AS integer) <25*/
                            ORDER BY  '.$sort.' '.$order.' LIMIT '.$limit.' OFFSET '.$offset.'
                        ;')->fetchAll(PDO::FETCH_ASSOC);
    //$res = $pdo->query('SELECT * FROM t_flow_total_per_cmpny;');
                            
    $res2 = $pdo->query(  'select count(*) as toplam FROM t_flow_total_per_cmpny
                            INNER JOIN t_cmpny as cmp ON cmp.id = t_flow_total_per_cmpny.cmpny_id
                            '.$sorguStr.'
                           /* WHERE 
                            CAST("Brass"->\'flow_properties\'->>\'quantity\' AS integer) >20*/
                            /*AND CAST("Brass"->\'flow_properties\'->>\'quantity\' AS integer) <25*/
                            ;'  )->fetchAll(PDO::FETCH_ASSOC);
    $companies = array();
    $company_details = array();
   foreach ($res as $company){
        //$companies[];
        //$company_details = array();
        foreach ($company AS $key=> $value) {
            //print_r($company);
            //echo ($company[$key]).'/n/l';
            if($key=='cmpny_id' ) {
                //print_r('company key---->'.$company[$key]);
                $company_details['id']= $company[$key];
                continue;
            } else if($key=='cmpny_name') {
                $company_details['company']= $value;
                continue;
            } else {
                $value = json_decode($value, true);
                //print_r($value);
                //print_r($value['column_name']);
                //print_r($value->column_name);
                
                if($value['flow_properties']['quantity']!=null) {
                    $company_details[trim($key)]= "<a href='#' id='".$company["cmpny_id"]."_1_".$value['column_name']."' class='easyui-tooltip'  >".$value['flow_properties']['quantity']." ".$value['flow_properties']['unit']." -".$value['flow_properties']['quality']."- ".$value['flow_properties']['flow_type']." </a>
                        <script>$('#".$company["cmpny_id"]."_1_".$value['column_name']."').tooltip({
                        position: 'right',
                        content: $('<div></div>'),
                        //content: '<span style=\"color:#fff\">This is the tooltip message.</span>',
                        onShow: function(){

                            $(this).tooltip('arrow').css('top', 20);
                            $(this).tooltip('tip').css('top', $(this).offset().top);
                        },
                        onUpdate: function(cc){
                            cc.panel({
                                width: 500,
                                height: 'auto',
                                border: false,
                                href: 'tooltip_ajax.php?id=".$company["cmpny_id"]."&col=".$value['column_name']."'
                            });
                        }

                    });</script> 

                    ";
                } 
                
            }
            
        }
     //print_r($company);
     //print_r($company['cmpny_name']);    
     //$test = json_encode($company);
     //print_r($test);
      $companies[] = $company_details;
    }

    $app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = $res2[0]['toplam'];
    $resultArray['rows'] = $companies;
    echo json_encode($resultArray);
    
});


/**
 * zeynel dağlı
 * @since 02-12-2014
 */
$app->get("/ISPotentialsNew_json_test", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']>0) {
             $offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
             $limit = intval($_GET['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = trim($_GET['sort']);
    } else {
        $sort = "id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    $inputOutputSearch=null;
    if(isset($_GET['IS']) && $_GET['IS']!="") {
        $inputOutputMut = trim($_GET['IS']);
        switch ($inputOutputMut) {
            case 1:
                $inputOutputSearch = "and a.flow_type_id=1
                                      and b.flow_type_id=1";
                break;
            case 2:
                $inputOutputSearch = "and a.flow_type_id=2
                                      and b.flow_type_id=2";
                break;
            case 3:
                $inputOutputSearch = "and a.flow_type_id=1
                                      and b.flow_type_id=2";
                break;
            case 0:
                $inputOutputSearch = null;
                break;
            default:
                $inputOutputSearch = "and a.flow_type_id=1
                                      and b.flow_type_id=1";
                break;
        }
    } else {
        $inputOutputSearch = null;
    }
    
    if(isset($_GET["selectedFlows"]) && $_GET["selectedFlows"]!=null && isset($_GET["companies"]) && $_GET["companies"]!=null) {
        $flowStr = rtrim($_GET["selectedFlows"], ",");
        $companyStr = rtrim($_GET["companies"], ",");
     
    $res = $pdo->query('SELECT 
                            a.cmpny_id,
                            a.flow_id,

                            b.cmpny_id,
                             b.flow_id ,
                             f.name as flow,
                             a.cmpny_id as id, 
                            cc.name as company,
                            a.qntty as a_miktar,
                            ua.name as qnttyunit,
                            fta.name as fromflowtype,
                            dd.id as tocompanyid,
                            dd.name as gittigifirma,
                            b.qntty as b_miktar,
                            ub.name as qntty2unit,
                            ftb.name as toflowtype,

                            f.id as flowid
                            FROM t_cmpny_flow AS a
                            INNER JOIN t_cmpny_flow AS b on b.flow_id = a.flow_id
                            inner join  t_cmpny cc on cc.id = a.cmpny_id
                            inner join  t_cmpny dd on dd.id = b.cmpny_id
                            inner join  t_unit ua on a.qntty_unit_id = ua.id
                            inner join  t_unit ub on b.qntty_unit_id = ub.id
                            inner join  t_flow_type fta on a.flow_type_id = fta.id
                            inner join  t_flow_type ftb on b.flow_type_id = ftb.id
                            inner join  t_flow f on b.flow_id = f.id
                            --RIGHT JOIN t_cmpny_flow AS b on b.flow_id = a.flow_id
                            where a.cmpny_id in ('.$companyStr.') 
                            and b.flow_id  in ('.$flowStr.') 
                            and a.cmpny_id<>b.cmpny_id
                            '.$inputOutputSearch.'
                            order by a.cmpny_id, b.flow_id;')->fetchAll(PDO::FETCH_ASSOC);
         
         
         
    $companies = array();
    foreach ($res as $company){
        
        $companies[]  = array(
            "id" => $company["id"].",".$company["tocompanyid"].",".$company["flowid"],
            "company" => $company["company"],
            "tocompany" => $company["gittigifirma"],
            "fromflowtype" => $company["fromflowtype"],
            "toflowtype" => $company["toflowtype"],
            "flow" => $company["flow"],
            //"qntty" => $company["qntty"],
            "qntty" => $company["a_miktar"],
            //"qntty2" => $company["qntty2"],
            "qntty2" => $company["b_miktar"],
            "qntty2unit" => $company["qntty2unit"],
            "qnttyunit" => $company["qnttyunit"]
        );
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    $app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = count($res);
    $resultArray['rows'] = $companies;
    echo json_encode($resultArray);
    } else {
        $arraySonuc = array("notFound"=>true);
        echo json_encode($arraySonuc);
    }

});


/**
 * zeynel dağlı
 * @since 02-12-2014
 */
$app->get("/companyFlows_json_test", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    $companyID=null;
    if(isset($_GET['companyid']) && $_GET['companyid']!="" ) {
        //$companyID = intval($_GET['companyid']);
        $companyID ="cmpny_id=".intval($_GET['companyid']);
    } else {
        $companyID=null;
    }
    
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']>0) {
             $offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
             $limit = intval($_GET['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = trim($_GET['sort']);
    } else {
        $sort = "id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    
    if(isset($_GET['IS2']) && $_GET['IS2']!="") {
        $inputOutputMut = trim($_GET['IS2']);
        switch ($inputOutputMut) {
            case 1:
                $inputOutputSearch = "and sm.flow_type_id=1 ";
                break;
            case 2:
                $inputOutputSearch = "and sm.flow_type_id=2";
                break;
            case 3:
                $inputOutputSearch = "and sm.flow_type_id=1";
                break;
            case 0:
                $inputOutputSearch = null;
                break;
            default:
                $inputOutputSearch = "and sm.flow_type_id=1";
                break;
        }
    } else {
        $inputOutputSearch = null;
    }
    
    $whereStr = null;
    if($inputOutputSearch!=null || $companyID!=null) $whereStr=' WHERE ';
    
    /*print_r("SELECT 
                            sm.flow_id as id,
                            f.name as flow,
                            sm.qntty as qntty,
                            u.name as unit,
                            sm.quality as quality,
                            ft.name as flowtype
                            FROM t_cmpny_flow AS sm
                            LEFT JOIN t_flow f on sm.flow_id = f.id
                            LEFT JOIN t_unit u on sm.qntty_unit_id = u.id
                            LEFT JOIN t_flow_type ft  on sm.flow_type_id = ft.id
                            ".$whereStr." ".$companyID."
                            ".$inputOutputSearch."
                            ORDER BY  ".$sort." ".$order." LIMIT ".$limit." OFFSET ".$offset."  ");*/
    
    $res = $pdo->query("SELECT 
                            sm.flow_id as id,
                            f.name as flow,
                            sm.qntty as qntty,
                            u.name as unit,
                            sm.quality as quality,
                            ft.name as flowtype
                            FROM t_cmpny_flow AS sm
                            LEFT JOIN t_flow f on sm.flow_id = f.id
                            LEFT JOIN t_unit u on sm.qntty_unit_id = u.id
                            LEFT JOIN t_flow_type ft  on sm.flow_type_id = ft.id
                            ".$whereStr." ".$companyID."
                            ".$inputOutputSearch."
                            ORDER BY  ".$sort." ".$order." LIMIT ".$limit." OFFSET ".$offset."  ")->fetchAll(PDO::FETCH_ASSOC);
                            //ORDER BY  ".$order." ".$sort." LIMIT ".$offset.",".$limit."
    $res2 = $pdo->query(  "SELECT 
                            count(*) as toplam
                            FROM t_cmpny_flow AS sm

                            ".$whereStr." ".$companyID."
                            ".$inputOutputSearch." ;"  )->fetchAll(PDO::FETCH_ASSOC);
    $flows = array();
    foreach ($res as $flow){
        $flows[]  = array(
            "id" => $flow["id"],
            "flow" => $flow["flow"],
            "qntty" => $flow["qntty"],
            "unit" => $flow["unit"],
            "quality" => $flow["quality"],
            "flowtype" => $flow["flowtype"],
        );
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    $app->response()->header("Content-Type", "application/json");
    
    /*if($inputOutputSearch!=null || $companyID!=null) {
        $resultArray = array();
        $resultArray['total'] = $res2[0]['toplam'];
        $resultArray['rows'] = $flows;
        //json_decode($_GET["selectedFlows"]);

        echo json_encode($resultArray);
    } else {
        $resultArray['data'] = $flows;
        echo json_encode($resultArray);
    }*/
    
    $resultArray = array();
    $resultArray['total'] = $res2[0]['toplam'];
    $resultArray['rows'] = $flows;
    //json_decode($_GET["selectedFlows"]);

    echo json_encode($resultArray);
    
    
});


/**
 * zeynel dağlı
 * @since 07-12-2014
 */
$app->get("/companyFlowsTooltip_json_test", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    if(isset($_GET['id']) && $_GET['id']!="" ) {
        $companyID = intval($_GET['id']);
    } 
    
    
   /* print_r("SELECT 
                            sm.flow_id as id,
                            f.name as flow,
                            sm.qntty as qntty,
                            u.name as unit,
                            sm.quality as quality,
                            ft.name as flowtype
                            FROM t_cmpny_flow AS sm
                            LEFT JOIN t_flow f on sm.flow_id = f.id
                            LEFT JOIN t_unit u on sm.qntty_unit_id = u.id
                            LEFT JOIN t_flow_type ft  on sm.flow_type_id = ft.id
                            WHERE cmpny_id=".$companyID."*/
    
    $res = $pdo->query("SELECT 
                            sm.flow_id as id,
                            f.name as flow,
                            sm.qntty as qntty,
                            u.name as unit,
                            sm.quality as quality,
                            ft.name as flowtype
                            FROM t_cmpny_flow AS sm
                            LEFT JOIN t_flow f on sm.flow_id = f.id
                            LEFT JOIN t_unit u on sm.qntty_unit_id = u.id
                            LEFT JOIN t_flow_type ft  on sm.flow_type_id = ft.id
                            WHERE cmpny_id=".$companyID."
 ")->fetchAll(PDO::FETCH_ASSOC);
                            //ORDER BY  ".$order." ".$sort." LIMIT ".$offset.",".$limit."
    
    $flows = array();
    foreach ($res as $flow){
        $flows[]  = array(
            "id" => $flow["id"],
            "flow" => $flow["flow"],
            "qntty" => $flow["qntty"],
            "unit" => $flow["unit"],
            "quality" => $flow["quality"],
            "flowtype" => $flow["flowtype"],
        );
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    $app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    //$resultArray['total'] = $res2[0]['toplam'];
    $resultArray['rows'] = $flows;
    //json_decode($_GET["selectedFlows"]);
    
    echo json_encode($resultArray);
    
});

/**
 * zeynel dağlı
 * @since 07-12-2014
 */
$app->get("/companyEquipmentTooltip_json_test", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    if(isset($_GET['id']) && $_GET['id']!="" ) {
        $companyID = intval($_GET['id']);
    } 
        
    $res = $pdo->query("SELECT 
                            sm.id as id,
                            f.name as company,
                            eq.name as equipment_name,
                            u.name as equipment_type,
                            ft.attribute_name AS equipment_attr
                            FROM t_cmpny_eqpmnt AS sm
                            LEFT JOIN t_cmpny f on sm.cmpny_id = f.id
                            LEFT JOIN t_eqpmnt eq on sm.eqpmnt_id = eq.id
                            LEFT JOIN t_eqpmnt_type u on sm.eqpmnt_type_id = u.id
                            LEFT JOIN t_eqpmnt_type_attrbt ft  on sm.eqpmnt_type_attrbt_id = ft.id
                            WHERE sm.cmpny_id=".$companyID.";
 ")->fetchAll(PDO::FETCH_ASSOC);
                            //ORDER BY  ".$order." ".$sort." LIMIT ".$offset.",".$limit."
    
    $flows = array();
    $eqpmntStr = null;
    if(!empty($res) ) {
        $eqpmntStr.="<p  style=\"font-size:14px\">".$res[0]["company"]." Equipment List</p>";
        foreach ($res as $flow){
            $eqpmntStr.="<ul>";
            $flows[]  = array(
                "id" => $flow["id"],
                "equipment_name" => $flow["equipment_name"],
                "equipment_type" => $flow["equipment_type"]
            );
            $eqpmntStr.="<li>equipment->".$flow["equipment_name"]."-- type-->".$flow["equipment_type"]."-- Attr->".$flow["equipment_attr"]."</li>";
            $eqpmntStr.="</ul>";
        }
    } else {
        $eqpmntStr.="<ul>";
            
            $eqpmntStr.="<li>No Equipment</li>";
            $eqpmntStr.="</ul>";
    }
    
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    $app->response()->header("Content-Type", "application/html");
    
    $resultArray = array();
    //$resultArray['total'] = $res2[0]['toplam'];
    $resultArray['rows'] = $flows;
    //json_decode($_GET["selectedFlows"]);
    echo $eqpmntStr;
    //echo json_encode($resultArray);
    
});


/**
 * zeynel dağlı
 * @since 02-12-2014
 */
$app->get("/flowCompanies_json_test", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    if(isset($_GET['flowid']) && $_GET['flowid']!="" ) {
        $flowID = intval($_GET['flowid']);
    } 
    
     if(isset($_GET['cmpny_id']) && $_GET['cmpny_id']!="" ) {
        $companyID = " AND sm.cmpny_id!=".intval($_GET['cmpny_id']);
    }
    
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        if($_GET['page']>0) {
            $offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
             $limit = intval($_GET['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }
        
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = trim($_GET['sort']);
    } else {
        $sort = "id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    
    if(isset($_GET['IS3']) && $_GET['IS3']!="") {
        $inputOutputMut = trim($_GET['IS3']);
        switch ($inputOutputMut) {
            case 1:
                $inputOutputSearch = "and sm.flow_type_id=1 ";
                break;
            case 2:
                $inputOutputSearch = "and sm.flow_type_id=2";
                break;
            case 3:
                $inputOutputSearch = "and sm.flow_type_id=2";
                break;
            case 0:
                $inputOutputSearch = null;
                break;
            default:
                $inputOutputSearch = "and sm.flow_type_id=1";
                break;
        }
    } else {
        $inputOutputSearch = null;
    }
    /*print_r("SELECT 
                            sm.cmpny_id as id,
                            cm.name as company,
                            sm.qntty as qntty,
                            u.name as unit,
                            sm.quality as quality,
                            ft.name as flowtype
                            FROM t_cmpny_flow AS  sm
                            LEFT JOIN t_flow f on sm.flow_id = f.id
                            LEFT JOIN t_unit u on sm.qntty_unit_id = u.id
                            LEFT JOIN t_flow_type ft  on sm.flow_type_id = ft.id
                            LEFT JOIN t_cmpny cm  on sm.cmpny_id= cm.id
                            WHERE flow_id=".$flowID." 
                            ".$companyID."
                            ".$inputOutputSearch."
                            ORDER BY  ".$sort." ".$order." LIMIT ".$limit." OFFSET ".$offset."  ");*/
    $res = $pdo->query("SELECT 
                            sm.cmpny_id as id,
                            cm.name as company,
                            sm.qntty as qntty,
                            u.name as unit,
                            sm.quality as quality,
                            ft.name as flowtype
                            FROM t_cmpny_flow AS  sm
                            LEFT JOIN t_flow f on sm.flow_id = f.id
                            LEFT JOIN t_unit u on sm.qntty_unit_id = u.id
                            LEFT JOIN t_flow_type ft  on sm.flow_type_id = ft.id
                            LEFT JOIN t_cmpny cm  on sm.cmpny_id= cm.id
                            WHERE flow_id=".$flowID." 
                            ".$companyID."
                            ".$inputOutputSearch."
                            ORDER BY  ".$sort." ".$order." LIMIT ".$limit." OFFSET ".$offset."  ")->fetchAll(PDO::FETCH_ASSOC);
                            //ORDER BY  ".$order." ".$sort." LIMIT ".$offset.",".$limit."
    $res2 = $pdo->query(  "SELECT 
                            count(*) as toplam
                            FROM t_cmpny_flow AS sm
                            
                            WHERE flow_id=".$flowID."
                            ".$companyID."
                            ".$inputOutputSearch." ;"  )->fetchAll(PDO::FETCH_ASSOC);
    $flows = array();
    foreach ($res as $flow){
        $flows[]  = array(
            "id" => $flow["id"],
            "company" => $flow["company"],
            "qntty" => $flow["qntty"],
            "unit" => $flow["unit"],
            "quality" => $flow["quality"],
            "flowtype" => $flow["flowtype"],
        );
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    $app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = $res2[0]['toplam'];
    $resultArray['rows'] = $flows;
    echo json_encode($resultArray);
    
});



/**
 * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/flowsAndCompanies_json_test", function () use ($app, $db, $pdo) {
    //$pdo->exec('SET NAMES "utf8"');
    //$res = $pdo->query(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ecoman_18_08' AND TABLE_NAME = 't_flow';"  )->fetchAll(PDO::FETCH_ASSOC);
    if(isset($_GET['flows']) && $_GET['flows']!="" ) {
        $flows = json_decode($_GET['flows'], true);
        //print_r($flows);
        $flowsStr="";
        foreach ($flows as $key=>$value){
            $flowsStr.= $value.',';
        }
        $flowsStr = rtrim($flowsStr, ',');
    } 
    //echo $flowsStr;
    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        $offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
        $limit = intval($_GET['rows']);
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = trim($_GET['sort']);
    } else {
        $sort = "id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    
    $res = $pdo->query("SELECT 
                            sm.cmpny_id as id,
                            cm.name as company,
                            f.name as flow,
                            sm.qntty as qntty,
                            u.name as unit,
                            sm.quality as quality,
                            ft.name as flowtype
                            FROM t_cmpny_flow sm
                            LEFT JOIN t_flow f on sm.flow_id = f.id
                            LEFT JOIN t_unit u on sm.qntty_unit_id = u.id
                            LEFT JOIN t_flow_type ft  on sm.flow_type_id = ft.id
                            LEFT JOIN t_cmpny cm  on sm.cmpny_id= cm.id
                            WHERE flow_id IN (".$flowsStr.")
                            ORDER BY  ".$sort." ".$order." LIMIT ".$limit." OFFSET ".$offset."  ")->fetchAll(PDO::FETCH_ASSOC);
                            //ORDER BY  ".$order." ".$sort." LIMIT ".$offset.",".$limit."
    $res2 = $pdo->query(  "SELECT 
                            count(*) as toplam
                            FROM t_cmpny_flow

                            WHERE flow_id IN (".$flowsStr.");"  )->fetchAll(PDO::FETCH_ASSOC);
    $flows = array();
    foreach ($res as $flow){
        $flows[]  = array(
            "id" => $flow["id"],
            "company" => $flow["company"],
            "qntty" => $flow["qntty"],
            "unit" => $flow["unit"],
            "quality" => $flow["quality"],
            "flowtype" => $flow["flowtype"],
            "flow" => $flow["flow"],
        );
    }
    
    //{field:'opportunity',title:'Opportunity',width:80},
    
    
    //$app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = $res2[0]['toplam'];
    $resultArray['rows'] = $flows;
    echo json_encode($resultArray);
    
});



$app->get("/books", function () use ($app, $db) {
    $books = array();
    foreach ($db->books() as $book) {
        $books[]  = array(
            "id" => $book["id"],
            "title" => $book["title"],
            "author" => $book["author"],
            "summary" => $book["summary"]
        );
    }
    $app->response()->header("Content-Type", "application/json");
    echo json_encode($books);
});

$app->get("/book/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $book = $db->books()->where("id", $id);
    if ($data = $book->fetch()) {
        echo json_encode(array(
            "id" => $data["id"],
            "title" => $data["title"],
            "author" => $data["author"],
            "summary" => $data["summary"]
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Book ID $id does not exist"
            ));
    }
}); 


/**
 * zeynel dağlı
 * @since 04-12-2014
 */
$app->get("/ISScenarios", function () use ($app, $db, $pdo) {

    if(isset($_GET['page']) && $_GET['page']!="" && isset($_GET['rows']) && $_GET['rows']!="") {
        $offset = ((intval($_GET['page'])-1)* intval($_GET['rows']));
        $limit = intval($_GET['rows']);
    } else {
        $limit = 10;
        $offset = 0;
    }
    
    if(isset($_GET['sort']) && $_GET['sort']!="") {
        $sort = trim($_GET['sort']);
    } else {
        $sort = "prj.id";
    }
    
    if(isset($_GET['order']) && $_GET['order']!="") {
        $order = trim($_GET['order']);
    } else {
        $order = "desc";
    }
    /*print_r("SELECT 
                prj.id AS prj_id, 
                prj.synergy_id, 
                prj.consultant_id, 
                prj.active, 
                prj.prj_date AS date, 
                prj.name AS prj_name,
                syn.name AS syn_name
            FROM t_is_prj as prj
            INNER JOIN t_synergy AS syn ON prj.synergy_id = syn.id
            ORDER BY  ".$sort." ".$order." LIMIT ".$limit." OFFSET ".$offset."  ");*/
    
    $res = $pdo->query("SELECT 
                            prj.id AS prj_id, 
                            prj.synergy_id, 
                            prj.consultant_id, 
                            prj.active, 
                            prj.prj_date AS date, 
                            prj.name AS prj_name,
                            syn.name AS syn_name
                        FROM t_is_prj as prj
                        INNER JOIN t_synergy AS syn ON prj.synergy_id = syn.id
                        ORDER BY  ".$sort." ".$order." LIMIT ".$limit." OFFSET ".$offset."  ")->fetchAll(PDO::FETCH_ASSOC);
    $res2 = $pdo->query(  "SELECT 
                            count(prj.id) as toplam
                            FROM t_is_prj as prj
                            INNER JOIN t_synergy AS syn ON prj.synergy_id = syn.id "  )->fetchAll(PDO::FETCH_ASSOC);
    $flows = array();
    foreach ($res as $flow){
        $flows[]  = array(
            "id" => $flow["prj_id"],
            "prj_name" => $flow["prj_name"],
            "syn_name" => $flow["syn_name"],
            "date" => $flow["date"],
        );
    }

    $resultArray = array();
    $resultArray['total'] = $res2[0]['toplam'];
    $resultArray['rows'] = $flows;
    echo json_encode($resultArray);
    
});




$app->post("/book", function () use($app, $db) {
    $app->response()->header("Content-Type", "application/text");
    echo "<pre>";
    print_r($app->request());
    echo "</pre>";
    $book = $app->request()->post();
    $result = $db->books->insert($book);
    echo json_encode(array("id" => $result["id"]));
});

$app->post("/books/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/text");
    $book = $db->books()->where("id", intval($id));
    //echo json_encode($db->books());
    //echo json_encode($book);
    if ($book->fetch()) {
        echo "<pre>";
        print_r($app->request());
        echo "</pre>";
        //$post = $app->request()->put();
        $post = $app->request()->post();
        print_r($post);
        $result = $book->update($post);
        echo json_encode(array(
            "post" => $post
            ));
        echo json_encode(array(
            "status" => (bool)$result,
            "message" => "Book updated successfully"
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Book id $id does not exist"
        ));
    }
});


$app->delete("/book/:id", function ($id) use($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $book = $db->books()->where("id", $id);
    if ($book->fetch()) {
        $result = $book->delete();
        echo json_encode(array(
            "status" => true,
            "message" => "Book deleted successfully"
        ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Book id $id does not exist"
        ));
    }
});


$app->run();
$pdo=null;

/*
$dsn = "mysql:dbname=deneme;host=localhost";
$username = "root";
$password = "";

$pdo = new PDO($dsn, $username, $password);
//$db = new NotORM($pdo);

$app = new Slim(array(
    "MODE" => "development",
    "TEMPLATES.PATH" => "./templates"
));

$app->get("/", function() {
    echo "<h1>Hello Slim World</h1>";
});

$app->get("/books", function () use ($app, $db) {
    $books = array();
    foreach ($db->books() as $book) {
        $books[]  = array(
            "id" => $book["id"],
            "title" => $book["title"],
            "author" => $book["author"],
            "summary" => $book["summary"]
        );
    }
    $app->response()->header("Content-Type", "application/json");
    echo json_encode($books);
});


$app->get("/book/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $book = $db->books()->where("id", $id);
    if ($data = $book->fetch()) {
        echo json_encode(array(
            "id" => $data["id"],
            "title" => $data["title"],
            "author" => $data["author"],
            "summary" => $data["summary"]
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Book ID $id does not exist"
            ));
    }
});

$app->post("/book", function () use($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $book = $app->request()->post();
    $result = $db->books->insert($book);
    echo json_encode(array("id" => $result["id"]));
});

$app->put("/book/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $book = $db->books()->where("id", $id);
    if ($book->fetch()) {
        $post = $app->request()->put();
        $result = $book->update($post);
        echo json_encode(array(
            "status" => (bool)$result,
            "message" => "Book updated successfully"
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Book id $id does not exist"
        ));
    }
});

$app->delete("/book/:id", function ($id) use($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $book = $db->books()->where("id", $id);
    if ($book->fetch()) {
        $result = $book->delete();
        echo json_encode(array(
            "status" => true,
            "message" => "Book deleted successfully"
        ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Book id $id does not exist"
        ));
    }
});

$app->run();
*/