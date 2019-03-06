<?php
require_once "admin-header.php";
echo "<title>问题导入结果</title>";
$info_html = "";
require_once "../include/check_post_key.php";
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']))) {
    require_once "./redirect_to_login.php";
    exit(1);
}
require_once "../include/const.inc.php";

function image_save_file($filepath, $base64_encoded_img)
{
    $fp = fopen($filepath, "wb");
    fwrite($fp, base64_decode($base64_encoded_img));
    fclose($fp);
}
require_once "../include/problem.php";
require_once "../include/db_info.inc.php";
function getLang($language)
{
    $language_name = $GLOBALS['language_name'];

    for ($i = 0; $i < count($language_name); $i++) {
        //echo "$language=$language_name[$i]=".($language==$language_name[$i]);
        if ($language == $language_name[$i]) {
            //$language=$i;
            //echo $language;
            return $i;
        }
    }
    return $i;
}
function submitSolution($pid, $solution, $language)
{
    global $OJ_NAME;
    $language = getLang($language);
    $len = mb_strlen($solution, 'utf-8');
    $sql = "INSERT INTO solution(problem_id,user_id,in_date,language,ip,code_length,result)
						VALUES(?,?,NOW(),?,'127.0.0.1',?,14)";
    $insert_id = pdo_query($sql, $pid, $_SESSION[$OJ_NAME . '_' . 'user_id'], $language, $len);
    //echo "submiting$language.....";
    $sql = "INSERT INTO `source_code`(`solution_id`,`source`)VALUES(?,?)";
    pdo_query($sql, $insert_id, $solution);
    $sql = "INSERT INTO `source_code_user`(`solution_id`,`source`)VALUES(?,?)";
    pdo_query($sql, $insert_id, $solution);
    pdo_query("update solution set result=1 where solution_id=?", $insert_id);
}
function getValue($Node, $TagName)
{

    return $Node->$TagName;
}
function getAttribute($Node, $TagName, $attribute)
{
    return $Node->children()->$TagName->attributes()->$attribute;
}
function hasProblem($title)
{
//return false;
    $md5 = md5($title);
    $sql = "select 1 from problem where md5(title)=?";
    $result = pdo_query($sql, $md5);
    $rows_cnt = count($result);

    //echo "row->$rows_cnt";
    return ($rows_cnt > 0);

}
function mkpta($pid, $prepends, $node)
{
    $language_ext = $GLOBALS['language_ext'];
    $OJ_DATA = $GLOBALS['OJ_DATA'];
    foreach ($prepends as $prepend) {
        $language = $prepend->attributes()->language;
        $lang = getLang($language);
        $file_ext = $language_ext[$lang];
        $basedir = "$OJ_DATA/$pid";
        $file_name = "$basedir/$node.$file_ext";
        file_put_contents($file_name, $prepend);
    }
}
function get_extension($file)
{
    $info = pathinfo($file);
    return $info['extension'];
}
function import_fps($tempfile)
{
    global $OJ_DATA, $OJ_SAE, $OJ_REDIS, $OJ_REDISSERVER, $OJ_REDISPORT, $OJ_REDISQNAME, $info_html;
    $xmlDoc = simplexml_load_file($tempfile, 'SimpleXMLElement', LIBXML_PARSEHUGE);
    $searchNodes = $xmlDoc->xpath("/fps/item");
    $spid = 0;
    foreach ($searchNodes as $searchNode) {
        //echo $searchNode->title,"\n";

        $title = $searchNode->title;

        $time_limit = $searchNode->time_limit;
        $unit = getAttribute($searchNode, 'time_limit', 'unit');
        //echo $unit;
        if ($unit == 'ms') {
            $time_limit /= 1000;
        }

        $memory_limit = getValue($searchNode, 'memory_limit');
        $unit = getAttribute($searchNode, 'memory_limit', 'unit');
        if ($unit == 'kb') {
            $memory_limit /= 1024;
        }

        $description = getValue($searchNode, 'description');
        $input = getValue($searchNode, 'input');
        $output = getValue($searchNode, 'output');
        $sample_input = getValue($searchNode, 'sample_input');
        $sample_output = getValue($searchNode, 'sample_output');
//        $test_input = getValue ( $searchNode, 'test_input' );
        //        $test_output = getValue ( $searchNode, 'test_output' );
        $hint = getValue($searchNode, 'hint');
        $source = getValue($searchNode, 'source');

        $spjcode = getValue($searchNode, 'spj');
        $spj = trim($spjcode) ? 1 : 0;
        if (!hasProblem($title)) {
            $pid = addproblem($title, $time_limit, $memory_limit, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA);
            if ($spid == 0) {
                $spid = $pid;
            }

            $basedir = "$OJ_DATA/$pid";
            mkdir($basedir);
            if (strlen($sample_input)) {
                mkdata($pid, "sample.in", $sample_input, $OJ_DATA);
            }

            if (strlen($sample_output)) {
                mkdata($pid, "sample.out", $sample_output, $OJ_DATA);
            }

            //      if(!isset($OJ_SAE)||!$OJ_SAE){
            $testinputs = $searchNode->children()->test_input;
            $testno = 0;

            foreach ($testinputs as $testNode) {
                //if($testNode->nodeValue)
                mkdata($pid, "test" . $testno++ . ".in", $testNode, $OJ_DATA);
            }
            unset($testinputs);
            $testinputs = $searchNode->children()->test_output;
            $testno = 0;
            foreach ($testinputs as $testNode) {
                //if($testNode->nodeValue)
                mkdata($pid, "test" . $testno++ . ".out", $testNode, $OJ_DATA);
            }
            unset($testinputs);
            // }
            $images = ($searchNode->children()->img);
            $did = array();
            $testno = 0;
            foreach ($images as $img) {
                //
                $src = getValue($img, "src");
                if (!in_array($src, $did)) {
                    $base64 = getValue($img, "base64");
                    $ext = pathinfo($src);
                    $ext = strtolower($ext['extension']);
                    if (!stristr(",jpeg,jpg,svg,png,gif,bmp", $ext)) {
                        $ext = "bad";
                        exit(1);
                    }
                    $testno++;
                    $newpath = "../upload/pimg" . $pid . "_" . $testno . "." . $ext;
                    if ($OJ_SAE) {
                        $newpath = "saestor://web/upload/pimg" . $pid . "_" . $testno . "." . $ext;
                    }

                    image_save_file($newpath, $base64);
                    $newpath = dirname($_SERVER['REQUEST_URI']) . "/../upload/pimg" . $pid . "_" . $testno . "." . $ext;
                    if ($OJ_SAE) {
                        $newpath = $SAE_STORAGE_ROOT . "upload/pimg" . $pid . "_" . $testno . "." . $ext;
                    }

                    $sql = "update problem set description=replace(description,?,?) where problem_id=?";
                    pdo_query($sql, $src, $newpath, $pid);
                    $sql = "update problem set input=replace(input,?,?) where problem_id=?";
                    pdo_query($sql, $src, $newpath, $pid);
                    $sql = "update problem set output=replace(output,?,?) where problem_id=?";
                    pdo_query($sql, $src, $newpath, $pid);
                    $sql = "update problem set hint=replace(hint,?,?) where problem_id=?";
                    pdo_query($sql, $src, $newpath, $pid);
                    array_push($did, $src);
                }

            }

            if (!isset($OJ_SAE) || !$OJ_SAE) {
                if ($spj) {
                    $basedir = "$OJ_DATA/$pid";
                    $fp = fopen("$basedir/spj.cc", "w");
                    fputs($fp, $spjcode);
                    fclose($fp);
                    ////system( " g++ -o $basedir/spj $basedir/spj.cc  ");
                    if (!file_exists("$basedir/spj")) {
                        $fp = fopen("$basedir/spj.c", "w");
                        fputs($fp, $spjcode);
                        fclose($fp);
                        ////system( " gcc -o $basedir/spj $basedir/spj.c  ");
                        if (!file_exists("$basedir/spj")) {
                            $info_html .= "<div class='alert alert-warning' role='alert'>你需要编译 $basedir/spj.cc -> spj[  g++ -o $basedir/spj $basedir/spj.cc   ]<br> 并且重判 $pid</div>";

                        } else {
                            unlink("$basedir/spj.cc");
                        }

                    }
                }
            }

            $solutions = $searchNode->children()->solution;
            foreach ($solutions as $solution) {
                $language = $solution->attributes()->language;
                submitSolution($pid, $solution, $language);
            }
            unset($solutions);
            $prepends = $searchNode->children()->prepend;
            mkpta($pid, $prepends, "prepend");
            $prepends = $searchNode->children()->template;
            mkpta($pid, $prepends, "template");
            $prepends = $searchNode->children()->append;
            mkpta($pid, $prepends, "append");
            $info_html .= "<div class='alert alert-success' role='alert'>$title 导入成功</div>";
        } else {
            $info_html .= "<div class='alert alert-warning' role='alert'>$title 已经在OJ中了</div>";
        }

    }
    unlink($tempfile);
    if (isset($OJ_REDIS) && $OJ_REDIS) {
        $redis = new Redis();
        $redis->connect($OJ_REDISSERVER, $OJ_REDISPORT);
        $sql = "select solution_id from solution where result=0 and problem_id>0";
        $result = pdo_query($sql);
        foreach ($result as $row) {
            echo $row['solution_id'] . "\n";
            $redis->lpush($OJ_REDISQNAME, $row['solution_id']);
        }
    }
}

if ($_FILES["fps"]["error"] > 0) {
    $info_html .= "<div class='alert alert-danger' role='alert'>错误: " . $_FILES['fps']['error'] . " 文件大小过大，请在 php.ini 中设置/div>";
} else {
    $tempfile = $_FILES["fps"]["tmp_name"];
    if (get_extension($_FILES["fps"]["name"]) == "zip") {
        $info_html .= "<div class='alert alert-warning' role='alert'>zip 文件 , 只有 fps/xml 文件 位于根目录时支持</div>";
        $resource = zip_open($tempfile);
        $i = 1;
        $tempfile = tempnam("/tmp", "fps");
        while ($dir_resource = zip_read($resource)) {
            if (zip_entry_open($resource, $dir_resource)) {
                $file_name = $path . zip_entry_name($dir_resource);
                $file_path = substr($file_name, 0, strrpos($file_name, "/"));
                if (!is_dir($file_name)) {
                    $file_size = zip_entry_filesize($dir_resource);
                    $file_content = zip_entry_read($dir_resource, $file_size);
                    file_put_contents($tempfile, $file_content);
                    import_fps($tempfile);
                }
                zip_entry_close($dir_resource);
            }
        }
        zip_close($resource);
        unlink($_FILES["fps"]["tmp_name"]);
    } else {
        import_fps($tempfile);
    }
//    echo "Upload: " . $_FILES ["fps"] ["name"] . "<br />";
    //    echo "Type: " . $_FILES ["fps"] ["type"] . "<br />";
    //    echo "Size: " . ($_FILES ["fps"] ["size"] / 1024) . " Kb<br />";
    //    echo "Stored in: " . $tempfile;

    //$xmlDoc = new DOMDocument ();
    //$xmlDoc->load ( $tempfile );
    //$xmlcontent=file_get_contents($tempfile );
}
?>

<!DOCTYPE html>
<html>
<?php require_once 'admin-header.php'?>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
  <?php require_once 'top_menubar.php';?>
  <?php require_once 'side_menubar.php';?>
  <div class="layui-body">
    <!-- 内容主体区域 -->
    <?php echo $info_html ?>
  </div>

</div>
<?php require_once 'js.php';?>
<script>
layui.use('element', function(){
  var element = layui.element;

});
</script>
</body>
</html>