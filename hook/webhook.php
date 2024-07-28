<?php
/**
 * Created by PhpStorm.
 * User: chunpat
 * Date: 28/2/2020
 * Time: 2:59 PM
 */

/**
 * 将字符串参数变为数组
 * @param $query
 * @return array
 */
function convertUrlQuery($query)
{
    $queryParts = $query ? explode('&', $query):[];
    $params = array();
    foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}

//拉取
function pull()
{
    //脚本验证
    $queryString = $_SERVER['QUERY_STRING'] ?? '';
    $args = convertUrlQuery($queryString);
    if (($args['go'] ?? '') !='1') {
        echo "unknow script";
        exit;
    }

    if(($args['test'] ?? '') != 1){
        //验证简单验证
        $body = file_get_contents('php://input');
        $body = json_decode($body, true);
        if ($body['ref'] != 'refs/heads/master') {
            echo 'not master branch push';
            exit;
        }
        error_reporting(7);
        date_default_timezone_set('UTC');
    }
    //获取所有修改的文件
//    $modifies = array_column($body['commits'], 'modified');
//    $modifyFiles = [];
//    foreach ($modifies as $modifyArr) {
//        foreach ($modifyArr as $modify) {
//            $modifyFiles[] = $modify;
//        }
//    }
//    $modifyFiles = array_unique($modifyFiles);
//
    $shell = '';
//    //配置nvm环境
//    $shell .= 'export PATH=$PATH:$HOME/.nvm/versions/node/v8.11.3/bin';
    //git拉去更新
    $shell .= 'cd %s && /usr/bin/git pull origin master:dev 2>&1';
//    //composer update判断
//    if (in_array('composer.json', $modifyFiles)) {
//        $shell .= ' && composer update 2>&1';
//    }
//    //npm i 判断
//    if (in_array('package.json', $modifyFiles)) {
//        $shell .= ' && npm i 2>&1';
//    }
//    //判断是否需要重新npm
//    $reg = '/\.(js|css|html)\"/i';
//    $modifyFilesStr = json_encode($modifyFiles);
//    if (preg_match($reg, $modifyFilesStr)) {
////            $shell .= " && npm run {$script} 2>&1";
//    }

    $pullPath = getcwd() . '/../';
    $logFile = getcwd() . '/git-hook.log';
    $shell = sprintf($shell, $pullPath);
    file_put_contents($logFile, $shell, FILE_APPEND);

//        $shell = sprintf("cd %s && git pull origin master 2>&1", $pullPath);
    $output = shell_exec($shell);
    $log = sprintf("[%s] %s \n", date('Y-m-d H:i:s', time()), $output);
    echo $log;
    file_put_contents($logFile, $log, FILE_APPEND);

    exit;
    //ln -s /usr/local/NODEJS_HOME/bin/node /usr/bin/node
}

pull();