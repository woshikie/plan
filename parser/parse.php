<?php
# DONE clean the data
$raw_data = file_get_contents("../raw_data/2014_2.html");

$raw_data = str_replace("<hr size=\"2\">", "", $raw_data);
$raw_data = str_replace("<hr>", "", $raw_data);
$raw_data = str_replace("<br>", "", $raw_data);
$raw_data = str_replace("&nbsp;", "", $raw_data);
$raw_data = preg_replace("/ +/", " ", $raw_data);
# print_r($raw_data);

$data =  new SimpleXMLElement($raw_data);
$data = $data->body->center;
$super_data = array();
foreach ($data->table as $course) {
    if ($course->tbody->tr[0]->td[0] !== null) { // course
        $course_code = (string) $course->tbody->tr[0]->td[0]->b->font[0];
        $course_name = (string) $course->tbody->tr[0]->td[1]->b->font[0];
        $course_au   = (string) $course->tbody->tr[0]->td[2]->b->font[0];
    } else { // index of the course
        
        $index_members = array();
        foreach ($course->tbody->tr as $index) {
            if ($index->td[0] == null) continue; // skip
            
            if (!empty($index->td[0]->b )) {
                if (isset($index_member)) { array_push($index_members,array(
                    "index_number" => $index_number,
                    "details" => $index_member)); }
                $index_number = (string) $index->td[0]->b;
                $index_member = array();
            }

            $member_type = (string) $index->td[1]->b;
            $member_group = (string) $index->td[2]->b;
            $member_day = (string) $index->td[3]->b;
            $member_time = (string) $index->td[4]->b;
            $member_location = (string) $index->td[5]->b;
            $member_remarks = (empty($index->td[6]->b)) ? "" : (string) $index->td[6]->b; // start on what week?
            array_push ($index_member, array(
                "type" => $member_type,
                "group" => $member_group,
                "day" => $member_day,
                "time" => $member_time,
                "location" => $member_location,
                "remarks" => $member_remarks));

            //$index_number = $index->td[0]->b;
            // this will be very dirty
            // 1 course only got 1 table for all index
            // 1 index consists of multiple rows, for different types
            // index starts with td[0] as number, otherwise empty
            // see 2014_2_data_1006_index.txt
            //$index_number = $index->td[0]->b;

        }
        if (isset($index_member)) { array_push($index_members,array(
            "index_number" => $index_number,
            "details" => $index_member)); }
        //$course_index   = $course->tbody->tr;
        array_push($super_data, array("code" => $course_code,
            "name" => $course_name,
            "au" => $course_au,
            "index" => $index_members));
    }


}

file_put_contents('../parsed_data_text/2014_2_data.txt', print_r($super_data, true));
file_put_contents('../parsed_data_json/2014_2_data.json', json_encode($super_data));

?>
