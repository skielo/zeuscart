<?php
/**
* GNU General Public License.

* This file is part of ZeusCart V4.

* ZeusCart V4 is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 4 of the License, or
* (at your option) any later version.
* 
* ZeusCart V4 is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with Foobar. If not, see <http://www.gnu.org/licenses/>.
*
*/


/**
 * This class contains functions to add a new user account and to update the cse into the database.
 *
 * @package  		Core_CAdminAddUsrRegsitration
 * @category  		Core
 * @author    		AjSquareInc Dev Team
 * @link   		    http://www.zeuscart.com
 * @copyright 		Copyright (c) 2008 - 2013, AjSquare, Inc.
 * @version  		Version 4.0.1
 */


class Core_CAdminAddUsrRegsitration
{
	
	/**
	 * Function adds a new user account into the users table.
	 * 
	 * 
	 * @return string
	 */
	function addAccount()
	{
		$displayname = $_POST['txtdisname'];
		$firstname = $_POST['txtfname'];
		$lastname = $_POST['txtlname'];
		$email = $_POST['txtemail'];
		$pswd = $_POST['txtpwd'];
		//$newsletter = $_POST['chknewsletter'];
		$date = date('Y-m-d');
		/*if($newsletter == '')
			$newsletter = 0;*/
		//address details
        $address= $_POST['txtaddr'];
        $city= $_POST['txtcity'];
        $state= $_POST['txtState'];
        $zipcode= $_POST['txtzipcode'];
        $country= $_POST['selCountry'];
			
		if(count($Err->messages) > 0)
		{
			 $output['val'] = $Err->values;
			 $output['msg'] = $Err->messages;
		}
		else
		{
			if( $displayname!= '' and $firstname  != '' and $lastname != '' and $email != '' and $pswd != '')
			{
                $sql = "INSERT INTO `users_table` (`user_display_name`, `user_fname`, `user_lname`, `user_email`, `user_pwd`, `user_group`, `user_country`, `user_status`, `user_doj`, `billing_address_id`, `shipping_address_id`, `ipaddress`, `social_link_id`, `is_from_social_link`, `confirmation_code`) 
                VALUES('".$displayname."','".$firstname."','".$lastname."', '".$email."','".$pswd."', '1', '" .$country ."', 1, '".$date."', 0, 0, '".$_SERVER['REMOTE_ADDR']."', '', 0, 0)";
			    $obj = new Bin_Query();
			
                if($obj->updateQuery($sql))
                {
                    $result = '<div class="alert alert-error">
                            <button data-dismiss="alert" class="close" type="button">×</button>
                            '.Core_CLanguage::_(ACCOUNT_CREATED).'
                            </div>';
                }
                else
                {
                    $result = '<div class="alert alert-error">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        '.Core_CLanguage::_(ACCOUNT_NOT_CREATED).'
                        </div>';
                    return $result;
                }
                
                //add address detail in address book
                $sq="select user_id from users_table where user_email='$email' and user_pwd='$pswd'";
                $qry1=new Bin_Query();
                $qry1->executeQuery($sq);
                
                if(count($qry1->records)>0)
                {
                    $newuserid=$qry1->records[0]['user_id'];
                    $adrsql="insert into addressbook_table(user_id,contact_name,first_name,last_name,company,email,address,city,suburb,state,country,zip,phone_no,fax) values($newuserid,'Primary','$firstname','$lastname','','$email','$address','$city','','$state','$country','$zipcode','','')";
                    $qry1->updateQuery($adrsql);
                    
                    //Add the code to insert the welcome promotional code
                    $sql_code = "SELECT `coupon_code` FROM `coupons_table` WHERE `coupan_name` = 'Welcome'";
                    $obj_code = new Bin_Query();
                    if($obj_code->executeQuery($sql_code))
                    {
                        $sql_coupon="INSERT INTO  coupon_user_relation_table(coupon_code, user_id, no_of_uses) VALUES ('" .$obj_code->records[0]['coupon_code']  ."'," .$newuserid .",0)";       
            
                        $objcode=new Bin_Query();
                        if($objcode->updateQuery($sql_coupon))
                        {
                            
                        }
                    }

                }else
                    $result = '<div class="alert alert-error">
                            <button data-dismiss="alert" class="close" type="button">×</button>
                            '.Core_CLanguage::_(ACCOUNT_NOT_CREATED).'
                            </div>';
			}
            else
                $result = '<div class="alert alert-error">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        '.Core_CLanguage::_(ACCOUNT_NOT_CREATED).'
                        </div>';
		}
        return $result;
   }
   
   /**
	 * Function updates the cse affiliate id in to the admin_settings table.
	 * 
	 * 
	 * @return string
	 */
   
   
   function saveCse()
   {
   		$registerid = $_POST['regid'];
		$csestatus = $_POST['chkregid'];
		if($csestatus == '')
			$csestatus = 0;
		if(count($Err->messages) > 0)
		{
			 $output['val'] = $Err->values;
			 $output['msg'] = $Err->messages;
		}
		else
		{
			if( $registerid!= '' and $csestatus  != '')
			{
				
			$sql = "update admin_settings table set set_value='".$registerid."' where set_name='www.pricerunner.com Affiliate ID'";
			$obj = new Bin_Query();
			
			if($obj->updateQuery($sql))
			{
				$result = "CSE settings Updated Successfully";
				return $result;
			}
			else
			{
				$result = "Error while updating CSE settings.";
				return $result;
			}
			}
		}
   } 
}
?>