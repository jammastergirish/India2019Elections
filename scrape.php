<?

function ParseResultsPage($state_html_data)
{
    $array_of_data = explode("<tr style='font-size:12px;'><td align='left'>", $state_html_data);

    $array_of_data[0] = '';

    $i=1;
    while($i<=count($array_of_data))
    {
        $ConstituencyName = explode("<", $array_of_data[$i])[0];
        $ConstituencyName = trim(ucwords(strtolower($ConstituencyName)));
        echo $ConstituencyName;

        echo "\t";

        $LeadingCandidate = explode("<td align='left'>", $array_of_data[$i])[1];
        $LeadingCandidate = trim(ucwords(strtolower(explode("<", $LeadingCandidate)[0])));
        echo $LeadingCandidate;

        echo "\t";

        $LeadingParty = explode("td align='left'><table><tbody><tr><td>", $array_of_data[$i])[1];
        $LeadingParty = trim(explode("<", $LeadingParty)[0]);
        echo $LeadingParty;

        echo "\t";

        $TrailingCandidate = explode("</td></tr></tbody></table></div></td></tr></tbody></table></td><td align='left'>", $array_of_data[$i])[1];
        $TrailingCandidate = trim(ucwords(strtolower(explode("<", $TrailingCandidate)[0])));
        echo $TrailingCandidate;

        echo "\t";

        $TrailingParty = explode("</td><td align='left'><table><tbody><tr><td>", $array_of_data[$i])[2];
        $TrailingParty = trim(explode("<", $TrailingParty)[0]);
        echo $TrailingParty;

        echo "\t";

        $Margin = explode("</td></tr></tbody></table></div></td></tr></tbody></table></td><td align='right'>", $array_of_data[$i])[1];
        $Margin = trim(explode("<", $Margin)[0]);
        echo $Margin;

        echo "\n\n";

        $LeadingParty2014 = explode("</td><td style='background-color: lightgray;' align='center'>", $array_of_data[$i])[2];
        $LeadingParty2014 = trim(explode("<", $LeadingParty2014)[0]);
        echo $LeadingParty2014;

        echo "\n\n";

        $line = $ConstituencyName."\t".$LeadingCandidate."\t".$LeadingParty."\t".$TrailingCandidate."\t".$TrailingParty."\t".$Margin."\t".$LeadingParty2014;
        // echo "\n\n".$line."\n";
        if ($line!="\t\t\t\t\t\t")
        {
            $myfile = file_put_contents('results_.tsv', $line.PHP_EOL , FILE_APPEND | LOCK_EX);
        }

        $i++;
    }
}

$StateCodes = ['U011', 'S011', 'S021', 'S031', 'S041', 'U021', 'S261', 'U031', 'U041', 'S051', 'S061', 'S071', 'S081', 'S091', 'S271', 'S101', 'S111', 'U061', 'S121', 'S131', 'S141', 'S151', 'S161', 'S171', 'U051', 'S181', 'U071', 'S191', 'S201', 'S211', 'S221', 'S291', 'S231', 'S241', 'S281', 'S251'];

unlink("results_.tsv");
file_put_contents('results_.tsv', "ConstituencyName\tLeadingCandidate\tLeadingParty\tTrailingCandidate\tTrailingParty\tMargin\tLeadingParty2014".PHP_EOL , FILE_APPEND | LOCK_EX);

foreach ($StateCodes as $StateCode)
{
    $url = "https://results.eci.gov.in/pc/en/trends/statewise".$StateCode.".htm?st=".$StateCode;

    $state_html_data = file_get_contents($url, false, stream_context_create($SWITCH_OFF_SSL_VERIFICATION));

    if (!$state_html_data)
    {
        echo "Failed ".$StateCode."\n\n";
    }
    else
    {
        echo $StateCode."\n******\n\n";

        $NumberOfConstituencies = explode("<span style='color: #00ff16; font-weight: bold; font-size: 16px;'>", $state_html_data)[1];
        $NumberOfConstituencies = explode("<", $NumberOfConstituencies)[0];
        $PagesOfResults = floor($NumberOfConstituencies/10)+1;
        echo $NumberOfConstituencies." and so ".$PagesOfResults." pages of results.";

        $j=1;
        while($j<=$PagesOfResults)
        {
            $StateCode = substr($StateCode, 0, -1).$j;
            $url = "https://results.eci.gov.in/pc/en/trends/statewise".$StateCode.".htm?st=".$StateCode;

            $state_html_data = file_get_contents($url, false, stream_context_create($SWITCH_OFF_SSL_VERIFICATION));

            ParseResultsPage($state_html_data);
            $j++;
        }
    }
}

copy("results_.tsv", "results.tsv");

?>