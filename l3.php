<?php
if (!isset($_POST['input'])) {
    $_POST['input'] = date('Y');
}
if (!isset($_POST['is_day'])) {
    $_POST['is_day'] = 'yes';
}
if ((!isset($_FILES['fileUpload']['tmp_name'])) || ($_FILES['fileUpload']['error'] > 0)) {
    $_FILES['fileUpload']['tmp_name'] = 'list.txt';
}

if (!checkFile($_FILES['fileUpload']['tmp_name'])) {
    echo "file may be unsafe!\n";
}
ob_start()
?>
<html>

<head>
    <style>
        td {
            text-align: right;
            padding: 0;
            border-collapse: collapse;
            width: 10px;
        }

        .month {
            height: 30vh;
            border: 10px solid #34495E;
            color: #95A5A6;
            background-color: #34495E;
        }

        .month td {
            padding-right: 10px;
        }

        .month tr {
            min-height: 4vh;
            background-color: transparent;
        }

        caption {
            padding-top: 10px;
            border: 10px solid #2C3E50;
            background-color: #2C3E50;
            color: #95A5A6;
        }

        input,
        textarea,
        select {
            background-color: #2C3E50;
            border: none;
            color: #95A5A6;
        }

        .sbm::-webkit-file-upload-button {
            visibility: hidden;
        }

        .sbm::before {
            content: 'choose festlist';
            color: #95A5A6;
            border-style: none;
        }


        .month td:hover {
            background-color: #666;
        }

        th {
            text-align: center;
            height: 50px
        }


        .fest {
            background-color: #866;
            font-weight: bold;
            color: red;
        }

        body {
            background-color: #444;
            color: #aaa;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            height: 100%;
        }

        .maincap {
            vertical-align: middle;
            border: 10px solid #22313F;
            background-color: #22313F;
            color: #95A5A6;
            font-family: Arial;

        }
    </style>
</head>

<body>
    <form method="post" enctype='multipart/form-data' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="number" name="input" min="1900" max="2099" step="1" value='<?php echo $_POST['input'] ?>' />
        <input type="file" class="sbm" name="fileUpload">
        to file?
        <input type="checkbox" name="save">
        <input type="submit" />

        <table style="width:100%">
            <caption class="maincap">
                Calendar <?php echo $_POST['input'];
                            if ($_POST['input'] == date('Y')) {
                                echo "(this)";
                            } ?>
            </caption>
            <?php createyear($_POST['input']);?>
        </table>
</body>
</html>
<?php
// file_put_contents($_POST['input'].'.html',ob_get_contents());

if (isset($_POST['save'])) {
    unset($_POST['save']);

    header('Pragma: anytextexeptno-cache', true);
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Type: text/plain");
    header("Content-Disposition: attachment; filename=\"" . $_POST['input'] . '.html' . "\"");
    echo $output;
}
function checkFile($file)
{
    foreach (file($file) as $key => $value) {
        $temp = explode('-', $value);
        if (sizeof($temp) != 3) {
            return false;
        } else {
            if ((is_int($temp[0])) && ($temp[0] < 32) && (0 < $temp[0]) && (is_int($temp[1])) && ($temp[1] < 13) && (0 < $temp[1])) {
                return false;
            }
        }
    }

    return true;
}

function createMonth($days, $frst, $fests, $fn)
{
    //print_r($fests);
    $d = 0;
    $days2 = $days;
    echo '<tr><th>mo</th><th>tue</th><th>we</th><th>th</th><th>fr</th><th>sa</th><th>su</th></tr>';

    $frst--;

    while ($days > 0) {
        echo '<tr>';
        while ($frst > 0) {
            echo '<td></td>';
            $d++;
            $frst--;
        }

        while ($d < 7) {
            $dday = (1 + $days2 - ($days--));
            if ($days < 0) {
                echo '<td>' . ' ' . '</td>';
            } else {
                if ((!empty($fests)) && (in_array($dday, $fests))) {
                    echo '<td class="fest" title="' . $fn[$dday] . '">' . $dday . '</td>';
                } else {
                    echo '<td>' . $dday . '</td>';
                }
            }
            $d++;
        }
        $d = 0;


        echo '</tr>';
    }
} ?>

<?php function createYear($year)
{

    // echo '<tr><th>yan</th><th>feb</th><th>mar</th><th>apr</tr> <tr></th>may<th>june</th><th>jully</th><th>aug</th></tr>';
    for ($i = 1; $i < 4; $i++) {
        echo '<tr>';
        for ($j = 1; $j < 5; $j++) {
            echo '<td><div>';
            $reds = (array) null;
            $redsNames = (array) null;
            echo '<table class="month">';
            echo '<caption>' . date('F', mktime(0, 0, 0, ($j + (($i - 1) * 4)), 10, $year)) . '</caption>';

            if (checkFile($_FILES['fileUpload']['tmp_name'])) {
                $filetoarr = file($_FILES['fileUpload']['tmp_name']);
                foreach ($filetoarr as $key => $value) {
                    // echo $value ;
                    $temp = explode('-', $value);
                    if ((sizeof($temp) > 1) && (($j + ($i - 1) * 4) == $temp[1])) {
                        array_push($reds, $temp[0]);

                        $redsNames[$temp[0]] = $temp[2];
                    }
                }
            }
            createMonth(date("t", mktime(0, 0, 0, ($j + ($i - 1) * 4), 1, $year)), date("N", mktime(0, 0, 0, ($j + ($i - 1) * 4), 1, $year)), $reds, $redsNames);
            echo '</table>';
            echo '</div></td>';
        }
        echo '</tr>';
    }
} ?>