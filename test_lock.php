<?php
echo microtime(true)."<br />";
	$start =  microtime(true);
	$fp = fopen("test_lock.dat","w");
	if (flock($fp, LOCK_EX | LOCK_NB)) {
        echo "Got lock!<br />";
        sleep(10);
        flock($fp, LOCK_UN);
    } else {
		echo "Cannot acquire lock<br />";
	}
	$stop =  microtime(true);
echo microtime(true)."<br />";
	echo "Duration: ".($stop-$start);
?>