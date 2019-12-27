<?php

// https://nl.wordpress.org/plugins/backwpup/
// voeg linkjes toe naar de goede pagina's zodat het makkelijker is.

// 1. Go to BackWPup and click on 'add new job'.
// 2. Under the general tab choose your 'Job Destination'.
// 3. Go to the tab schedule.
// 4. At 'Start Job' choose 'with a link'.
// 5. Fill in the link in the field below.
// 6. Let's run a test to see if everything works as intended.



// 1. Go to the BackWPup Settings.
// 2. Go to the tab 'Jobs' and fill in the 'Key to start jobs externally with an URL' key in the field here below.
// 3. Create a new job, choose the 'Job Destination' and fill in the the job id in the field below.
// 4. Go to the tab 'Schedule' and choose the option 'with a link' and hit save.
// 5. Let's run a test to see if everything works as intended.

$domainName = "https://tidywp.sparknowmedia.com";
$nonceBackWPup = "6a295588";
$jobID = "1";

$domainName + "/wp-cron.php?_nonce=" + $nonceBackWPup + "&backwpup_run=runext&jobid=" + $jobID;