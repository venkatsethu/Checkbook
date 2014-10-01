<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<?php

$spending_parameter_mapping = _checkbook_smart_search_domain_fields('spending', $IsOge);

if($IsOge)
    $linkable_fields = array(
        "oge_agency_name" => "/spending_landing/category/".$spending_results['spending_category_id'].'/datasource/checkbook_oge'."/year/" . _getCurrentYearID() . "/yeartype/B/agency/".$spending_results["agency_id"],
        "vendor_name" => "/spending_landing/category/".$spending_results['spending_category_id'].'/datasource/checkbook_oge'.  '/agency/' . $spending_results['agency_id'] . "/year/" . _getCurrentYearID() . "/yeartype/B/vendor/".$spending_results["vendor_id"],
    );
else
{
    if($spending_results['is_prime_or_sub'] == 'S'){
        $linkable_fields = array(
            "agency_name" => "/spending_landing/category/".$spending_results['spending_category_id']."/year/" . _getCurrentYearID() . "/yeartype/B/agency/".$spending_results["agency_id"],
            "vendor_name" => "/spending_landing/category/".$spending_results['spending_category_id']."/year/" . _getCurrentYearID() . "/yeartype/B/subvendor/".$spending_results["vendor_id"],
        );
    }
    else{
        $linkable_fields = array(
            "agency_name" => "/spending_landing/category/".$spending_results['spending_category_id']."/year/" . _getCurrentYearID() . "/yeartype/B/agency/".$spending_results["agency_id"],
            "vendor_name" => "/spending_landing/category/".$spending_results['spending_category_id']."/year/" . _getCurrentYearID() . "/yeartype/B/vendor/".$spending_results["vendor_id"],
        );
    }

}


if(!$IsOge)
$date_fields = array("check_eft_issued_date");
$amount_fields = array("check_amount");
$count = 1;
$row = array();
$rows = array();
$spending_results["check_eft_issued_date"] = ($IsOge)? "N/A" : $spending_results["check_eft_issued_date"];
foreach ($spending_parameter_mapping as $key=>$title){
  if($key == 'expenditure_object_name'){
    $value = $spending_results[$key][0];
  }
  else{
    $value = $spending_results[$key];
  }

    // highlighting (italics) search term
  $temp = substr($value, strpos(strtoupper($value), strtoupper($SearchTerm)),strlen($SearchTerm));
  $value = str_ireplace($SearchTerm,'<em>'. $temp . '</em>', $value);

  if(array_key_exists($key, $linkable_fields)){
    $value = "<a href='" . $linkable_fields[$key] ."'>". $value ."</a>";
  }
  else if(in_array($key, $date_fields)){
    $value = date("F j, Y", strtotime($value));
  }else if(in_array($key, $amount_fields)){
    $value = custom_number_formatter_format($value, 2 , '$');
  }
  if($key == 'contract_number' &&  $spending_results['agreement_id']){
    $value = "<a class=\"new_window\" href=\"/contract_details"
      . (($IsOge)?'/datasource/checkbook_oge' :'')
      . _checkbook_project_get_contract_url($value, $spending_results['agreement_id']) . '/newwindow\">'
      . $value . "</a>";
  }
  if($key == "vendor_name" && !$spending_results["vendor_id"]){
    $value = $spending_results["vendor_name"];
  }
  if($key == "minority_type_name" && !$spending_results["minority_type_name"]){
    $value = 'N/A';
  }
  if($key == "minority_type_name" && $spending_results["minority_type_name"]){
      $id = $spending_results["minority_type_id"];
      if($id == '4' || $id == '5'){
          $id = '4~5';
      }
      $value = "<a href='/spending_landing/yeartype/B/year/115/mwbe/".$id ."'>" .$spending_results["minority_type_name"] ."</a>";
  }
  if($key == 'is_prime_or_sub' && $spending_results["is_prime_or_sub"] == 'P' ){
    $value = 'No';
  }
  if($key == 'is_prime_or_sub' && $spending_results["is_prime_or_sub"] == 'S' ){
    $value = 'Yes';
  }
  if($key == 'prime_vendor_name' && $spending_results["is_prime_or_sub"] == 'P' ){
     $value = 'N/A';
  }
  if ($count % 2 == 0){
    if($title)
        $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'.$value.'</div>';
    $rows[] = $row;
    $row = array();
  } else {
    if($title)
        $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'.$value.'</div>';
  }
  $count++;
}
print theme('table',array('rows'=>$rows,'attributes'=>array('class'=>array('search-result-fields'))));
