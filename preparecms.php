<?php

// preparecms.php
// Version 1.0 - 20131003110113
// 
// A program to read YouTube CMS monthly report files
// and create relevant csv files to be imported into
// the appropriate SQL tables.
// Author: Carlos Granier @cgranier
// http://cgranier.com
//

$debugFlag = FALSE;

// Read the command line arguments

if ($argc < 2) {
    echo "\n";
    echo "+-------------------------------+\n";
    echo "+ ERROR: Insufficient arguments +\n";
    echo "+ USAGE:                        +\n";
    echo "+ php preparecms.php filename   +\n";
    echo "+-------------------------------+\n\n";
}
else {
    // Determine Year and Month from filename
    $filename = $argv[1];
    $pattern = '(\d{8})';
    preg_match($pattern, $filename, $matches, PREG_OFFSET_CAPTURE, 3);
        if ($debugFlag) { print_r($matches); }
        if ($debugFlag) { print_r($matches[0]); }
    $year = intval(substr($matches[0][0],0,4));
    $month = intval(substr($matches[0][0],4,2));
    // This could be optimized
    $filenameDate = substr($matches[0][0],0,4) . substr($matches[0][0],4,2);

    // Read the YouTube CMS Report file into $data
    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (!feof($handle)) {
            $data[] = fgetcsv($handle, 1000, ",");
        }
    fclose($handle);

    // This is what our csv fields should look like. If different, we need to look at the csv
    // file and determine what YouTube has changed in the reports
        $modelMonthlyTotals = array("Total Views","Watch Page Views","Embedded Player Views","Channel Page Video Views","Live Views","Recorded Views","Ad-Enabled Views","Ad-Requested Views","Total Earnings","Net YouTube-sold Revenue","Net AdSense-sold Revenue","Estimated RPM","Gross Revenue","Gross YouTube-sold Revenue","Gross Partner-sold Revenue","Gross AdSense-sold Revenue");
        $modelDailyTotals = array("Day","Content Type","Policy","Total Views","Watch Page Views","Embedded Player Views","Channel Page Video Views","Live Views","Recorded Views","Ad-Enabled Views","Ad-Requested Views","Total Earnings","Gross YouTube-sold Revenue","Gross Partner-sold Revenue","Gross AdSense-sold Revenue","Estimated RPM","Net YouTube-sold Revenue","Net AdSense-sold Revenue");
        $modelGeoTotals = array("Country","Content Type","Policy","Total Views","Watch Page Views","Embedded Player Views","Channel Page Video Views","Live Views","Recorded Views","Ad-Enabled Views","Ad-Requested Views","Total Earnings","Gross YouTube-sold Revenue","Gross Partner-sold Revenue","Gross AdSense-sold Revenue","Estimated RPM","Net YouTube-sold Revenue","Net AdSense-sold Revenue");
        $modelVideoTotals = array("Video ID","Content Type","Policy","Video Title","Video Duration (sec)","Username","Uploader","Claim Type","Claim Origin","Total Views","Watch Page Views","Embedded Player Views","Channel Page Video Views","Live Views","Recorded Views","Ad-Enabled Views","Ad-Requested Views","Total Earnings","Gross YouTube-sold Revenue","Gross Partner-sold Revenue","Gross AdSense-sold Revenue","Estimated RPM","Net YouTube-sold Revenue","Net AdSense-sold Revenue","Multiple Claims?","Category","Asset ID","Channel","Custom ID");

    // These are the sanitized fieldnames we will use in the final csv files to make importing
    // into the SQL database easier.
        $dbHeadersMonthlyTotals = array("totalViews","watchPageViews","embeddedPlayerViews","channelPageVideoViews","liveViews","recordedViews","adEnabledViews","adRequestedViews","totalEarnings","netYouTubeSoldRevenue","netAdSenseSoldRevenue","estimatedRPM","grossRevenue","grossYouTubeSoldRevenue","grossPartnerSoldRevenue","grossAdSenseSoldRevenue","year","month");
        $dbHeadersDailyTotals = array("date","contentType","policy","totalViews","watchPageViews","embeddedPlayerViews","channelPageVideoViews","liveViews","recordedViews","adEnabledViews","adRequestedViews","totalEarnings","grossYouTubeSoldRevenue","grossPartnerSoldRevenue","grossAdSenseSoldRevenue","estimatedRPM","netYouTubeSoldRevenue","netAdSenseSoldRevenue");
        $dbHeadersGeoTotals = array("country","contentType","policy","totalViews","watchPageViews","embeddedPlayerViews","channelPageVideoViews","liveViews","recordedViews","adEnabledViews","adRequestedViews","totalEarnings","grossYouTubeSoldRevenue","grossPartnerSoldRevenue","grossAdSenseSoldRevenue","estimatedRPM","netYouTubeSoldRevenue","netAdSenseSoldRevenue","year","month");
        $dbHeadersVideoTotals = array("videoId","contentType","policy","videoTitle","videoDuration","username","uploader","claimType","claimOrigin","totalViews","watchPageViews","embeddedPlayerViews","channelPageVideoViews","liveViews","recordedViews","adEnabledViews","adRequestedViews","totalEarnings","grossYouTubeSoldRevenue","grossPartnerSoldRevenue","grossAdSenseSoldRevenue","estimatedRPM","netYouTubeSoldRevenue","netAdSenseSoldRevenue","multipleClaims","category","assetId","channel","customId","year","month");

    // Create the MonthlyTotals data table and save as a csv file for importing it into SQL database
    // TABLE NAME: MonthlyTotals
    // COLUMNS:
    // [0] Total Views -> totalViews
    // [1] Watch Page Views -> watchPageViews
    // [2] Embedded Player Views -> embeddedPlayerViews
    // [3] Channel Page Video Views -> channelPageVideoViews
    // [4] Live Views -> liveViews
    // [5] Recorded Views -> recordedViews
    // [6] Ad-Enabled Views -> adEnabledViews
    // [7] Ad-Requested Views -> adRequestedViews

    // [8] Total Earnings -> totalEarnings
    // [9] Net YouTube-sold Revenue -> netYouTubeSoldRevenue
    // [10] Net AdSense-sold Revenue -> netAdSenseSoldRevenue
    // [11] Estimated RPM -> estimatedRPM

    // [12] Gross Revenue -> grossRevenue
    // [13] Gross YouTube-sold Revenue -> grossYouTubeSoldRevenue
    // [14] Gross Partner-sold Revenue -> grossPartnerSoldRevenue
    // [15] Gross AdSense-sold Revenue -> grossAdSenseSoldRevenue

    // We will add two fields:
    // [17] year = parse file name for current year
    // [16] month = parse file name for current month

        $row = 0;
        while ($data[$row][0] !== "Totals") {
            $row++;
        }
        $row++;
        // create the header
        $headerMonthlyTotals = $data[$row];
        // print_r($headerMonthlyTotals);
        $row++;
        // create the data row
        $dataMonthlyTotals = $data[$row];
        $row++;
        if ($debugFlag) { print_r($data[$row]); } 
        $row++;
        // add the "Total Earnings" fields to the header
        foreach ($data[$row] as $field) {
            $headerMonthlyTotals[] = $field;
        }
        $row++;
        // add the "Total Earnings" data to the data row
        foreach ($data[$row] as $field) {
            $dataMonthlyTotals[] = $field;
        }
        $row++;
        $row++;
        // add the "Gross Revenue" fields to the header
        foreach ($data[$row] as $field) {
            $headerMonthlyTotals[] = $field;
        }
        $row++;
        // add the "Gross Revenue" data to the data row
        foreach ($data[$row] as $field) {
            $dataMonthlyTotals[] = $field;
        }
        $row++;
        $row++;
        // check if headers match our model
        if ($modelMonthlyTotals !== $headerMonthlyTotals) {
            echo "\n";
            echo "+-------------------------------+\n";
            echo "+ ERROR:                        +\n";
            echo "+ Monthly Total Headers         +\n";
            echo "+ DO NOT MATCH MODEL            +\n";
            echo "+-------------------------------+\n\n";
        } else {
            // add the year and month to the data
            array_push($dataMonthlyTotals, $year, $month); 
            if ($debugFlag) { print_r($headerMonthlyTotals); }
            if ($debugFlag) { print_r($dataMonthlyTotals); }
            if ($debugFlag) { echo "\n"; }

            // Create MonthlyTotals file here
            $monthlyTotalsFilename = $filenameDate . "_monthlyTotals_.csv";
                echo "\n";
                echo "Writing out file " . $monthlyTotalsFilename . "\n";
            $fp = fopen($monthlyTotalsFilename, 'w');
                fputcsv($fp, $dbHeadersMonthlyTotals);
                fputcsv($fp, $dataMonthlyTotals);
                echo "\n";
            // Close it once we're all done
            fclose($fp);
        }
    // Create the DailyTotals data table and save as a csv file for importing it into SQL database
    // TABLE NAME: DailyTotals
    // COLUMNS:
    //  [0] Day -> date
    //  [1] Content Type -> contentType
    //  [2] Policy -> policy
    //  [3] Total Views -> totalViews
    //  [4] Watch Page Views -> watchPageViews
    //  [5] Embedded Player Views -> embeddedPlayerViews
    //  [6] Channel Page Video Views -> channelPageVideoViews
    //  [7] Live Views -> liveViews
    //  [8] Recorded Views -> recordedViews
    //  [9] Ad-Enabled Views -> adEnabledViews
    // [10] Ad-Requested Views -> adRequestedViews
    // [11] Total Earnings -> totalEarnings
    // [12] Gross YouTube-sold Revenue -> grossYouTubeSoldRevenue
    // [13] Gross Partner-sold Revenue -> grossPartnerSoldRevenue
    // [14] Gross AdSense-sold Revenue -> grossAdSenseSoldRevenue
    // [15] Estimated RPM -> estimatedRPM
    // [16] Net YouTube-sold Revenue -> netYouTubeSoldRevenue
    // [17] Net AdSense-sold Revenue -> netAdSenseSoldRevenue

        // create the header
        $headerDailyTotals = $data[$row];
        if ($debugFlag) { print_r($headerDailyTotals); }
        $row++;

        // create the data rows
        $dataRow = 0;
        while (!empty($data[$row][0])) {
            $dataDailyTotals[$dataRow] = $data[$row];
            $row++;
            $dataRow++;
        }
        $row++;
        if ($debugFlag) { print_r($data[$row]); }

        // check if headers match our model
        if ($modelDailyTotals !== $headerDailyTotals) {
            echo "\n";
            echo "+-------------------------------+\n";
            echo "+ ERROR:                        +\n";
            echo "+ Daily Total Headers           +\n";
            echo "+ DO NOT MATCH MODEL            +\n";
            echo "+-------------------------------+\n\n";
        } else {
            // Create DailyTotals file here
            $dailyTotalsFilename = $filenameDate . "_dailyTotals_.csv";
                echo "Writing out file " . $dailyTotalsFilename . "\n";
            $fp = fopen($dailyTotalsFilename, 'w');
                fputcsv($fp, $dbHeadersDailyTotals);
                foreach($dataDailyTotals as $rowData) {
                    fputcsv($fp, $rowData);
                    echo ".";
                }
                echo "*\n";
            // Close it once we're all done
            fclose($fp);
        }
    // Create the MonthlyGeoTotals data table and save as a csv file for importing it into SQL database
    // TABLE NAME: MonthlyGeoTotals
    // COLUMNS:
    //  [0] Country -> country
    //  [1] Content Type -> contentType
    //  [2] Policy -> policy
    //  [3] Total Views -> totalViews
    //  [4] Watch Page Views -> watchPageViews
    //  [5] Embedded Player Views -> embeddedPlayerViews
    //  [6] Channel Page Video Views -> channelPageVideoViews
    //  [7] Live Views -> liveViews
    //  [8] Recorded Views -> recordedViews
    //  [9] Ad-Enabled Views -> adEnabledViews
    // [10] Ad-Requested Views -> adRequestedViews
    // [11] Total Earnings -> totalEarnings
    // [12] Gross YouTube-sold Revenue -> grossYouTubeSoldRevenue
    // [13] Gross Partner-sold Revenue -> grossPartnerSoldRevenue
    // [14] Gross AdSense-sold Revenue -> grossAdSenseSoldRevenue
    // [15] Estimated RPM -> estimatedRPM
    // [16] Net YouTube-sold Revenue -> netYouTubeSoldRevenue
    // [17] Net AdSense-sold Revenue -> netAdSenseSoldRevenue

    // We will add two fields:
    // [18] year = parse file name for current year
    // [19] month = parse file name for current month

        $headerGeoTotals = $data[$row];
        if ($debugFlag) { print_r($headerGeoTotals); }
        $row++;

        // create the data rows
        $dataRow = 0;
        while (!empty($data[$row][0])) {
            $dataGeoTotals[$dataRow] = $data[$row];
            $row++;
            $dataRow++;
        }
        $row++;
        if ($debugFlag) { print_r($data[$row]); }

        // check if headers match our model
        if ($modelGeoTotals !== $headerGeoTotals) {
            echo "\n";
            echo "+-------------------------------+\n";
            echo "+ ERROR:                        +\n";
            echo "+ Geo Total Headers           +\n";
            echo "+ DO NOT MATCH MODEL            +\n";
            echo "+-------------------------------+\n\n";
        } else {
            // Create GeoTotals file here
            $geoTotalsFilename = $filenameDate . "_geoTotals_.csv";
                echo "Writing out file " . $geoTotalsFilename . "\n";
            $fp = fopen($geoTotalsFilename, 'w');
                fputcsv($fp, $dbHeadersGeoTotals);
                foreach($dataGeoTotals as $rowData) {
                    array_push($rowData, $year, $month); 
                    fputcsv($fp, $rowData);
                    echo ".";
                }
                echo "*\n";
            // Close it once we're all done
            fclose($fp);
        }
    // Create the MonthlyVideoTotals data table and save as a csv file for importing it into SQL database
    // TABLE NAME: MonthlyVideoTotals
    // COLUMNS:
    //  [0] Video ID -> videoId
    //  [1] Content Type -> contentType
    //  [2] Policy -> policy
    //  [3] Video Title -> videoTitle
    //  [4] Video Duration (sec) -> videoDuration
    //  [5] Username -> username
    //  [6] Uploader -> uploader
    //  [7] Claim Type -> claimType
    //  [8] Claim Origin -> claimOrigin
    //  [9] Total Views -> totalViews
    // [10] Watch Page Views -> watchPageViews
    // [11] Embedded Player Views -> embeddedPlayerViews
    // [12] Channel Page Video Views -> channelPageVideoViews
    // [13] Live Views -> liveViews
    // [14] Recorded Views -> recordedViews
    // [15] Ad-Enabled Views -> adEnabledViews
    // [16] Ad-Requested Views -> adRequestedViews
    // [17] Total Earnings -> totalEarnings
    // [18] Gross YouTube-sold Revenue -> grossYouTubeSoldRevenue
    // [19] Gross Partner-sold Revenue -> grossPartnerSoldRevenue
    // [20] Gross AdSense-sold Revenue -> grossAdSenseSoldRevenue
    // [21] Estimated RPM -> estimatedRPM
    // [22] Net YouTube-sold Revenue -> netYouTubeSoldRevenue
    // [23] Net AdSense-sold Revenue -> netAdSenseSoldRevenue
    // [24] Multiple Claims? -> multipleClaims
    // [25] Category -> category
    // [26] Asset ID -> assetId
    // [27] Channel -> channel
    // [28] Custom ID -> customId

    // We will add two fields:
    // [29] year = parse file name for current year
    // [30] month = parse file name for current month

        $headerVideoTotals = $data[$row];
        if ($debugFlag) { print_r($headerVideoTotals); }
        $row++;

        // create the data rows
        $dataRow = 0;
        while (!empty($data[$row][0])) {
            $dataVideoTotals[$dataRow] = $data[$row];
            $row++;
            $dataRow++;
        }
        if ($debugFlag) { print_r($dataVideoTotals); }

        // check if headers match our model
        if ($modelVideoTotals !== $headerVideoTotals) {
            echo "\n";
            echo "+-------------------------------+\n";
            echo "+ ERROR:                        +\n";
            echo "+ Video Total Headers           +\n";
            echo "+ DO NOT MATCH MODEL            +\n";
            echo "+-------------------------------+\n\n";

            print_r($headerVideoTotals);
            print_r($modelVideoTotals);
        } else {
            // Create VideoTotals file here
            $videoTotalsFilename = $filenameDate . "_videoTotals.csv";
                echo "Writing out file " . $videoTotalsFilename . "\n";
            $fp = fopen($videoTotalsFilename, 'w');
                fputcsv($fp, $dbHeadersVideoTotals);
                foreach($dataVideoTotals as $rowData) {
                    array_push($rowData, $year, $month); 
                    fputcsv($fp, $rowData);
                    echo ".";
                }
                echo "*\n";
            // Close it once we're all done
            fclose($fp);
        }
    }
}
?>
