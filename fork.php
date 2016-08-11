<?php 
$pids = array();
$child_pid = pcntl_fork();

if ($child_pid == -1)
{
    throw new Exception( __METHOD__ . "|" . __LINE__ .
            ": fork() error");
}
else if ($child_pid)
{
    //parent 
    exit(0);
}
else
{
    //child
    for($i=0;$i<3;$i++)
    {
        $child_pid = pcntl_fork();
        if($child_pid)
        {
            //parent
            $pids[] = $child_pid;
            sleep(5);
            print_r($pids);echo "\n";
        }else{
            //child
            break;
        }
    }
}

while(1)
{
    //your code
    $file = popen("php 10000.php start -d","r");
    $file = popen("php 10001.php start -d","r");
    $file = popen("php 10002.php start -d","r");
    $file = popen("php 10003.php start -d","r");
    $file = popen("php 10004.php start -d","r");
    $file = popen("php 10005.php start -d","r");
    $file = popen("php 10006.php start -d","r");
    $file = popen("php 10007.php start -d","r");
    $file = popen("php 10008.php start -d","r");
    $file = popen("php 10009.php start -d","r");
    $file = popen("php 10010.php start -d","r");
    $file = popen("php 10011.php start -d","r");
    $file = popen("php 10012.php start -d","r");
    $file = popen("php 10013.php start -d","r");
    $file = popen("php 10014.php start -d","r");
    $file = popen("php 10015.php start -d","r");
    $file = popen("php 10016.php start -d","r");
    $file = popen("php 10017.php start -d","r");
    $file = popen("php 10018.php start -d","r");
    $file = popen("php 10019.php start -d","r");
    $file = popen("php 10020.php start -d","r");
    $file = popen("php 10021.php start -d","r");
    $file = popen("php 10022.php start -d","r");
    $file = popen("php 10023.php start -d","r");
    $file = popen("php 10024.php start -d","r");
    sleep(1);
}	
?>