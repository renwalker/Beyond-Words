<?php

# Adapted from https://github.com/constantcontact/Constant-Contact-PHP-Sample-Contact-Forms/blob/master/simple_form.php

function handle_newsletter($form_name, $listID = 1, $thankyouUrl = '') {
//	if ($listID)
    $return = array('message' => '', 'inline_errors' => array());
	$redirectToThankYou = 0;
    if (!empty($_POST) && isset($_POST['form_name']) && $_POST['form_name'] == $form_name) {

        try {

            #we don't have closures 
            function array_walk_trim_callback(&$v, $k) {
                $v = trim($v);
            }

            include_once './templates/default_site/contact.group/cc_class.php';
            $ccContactOBJ = new CC_Contact();
            $disabled = "";

            $postFields = array();

            $postFields["first_name"] = '';
            $postFields["middle_name"] = '';
            $postFields["last_name"] = '';

            if(isset($_POST['full_name']))
            {
                $full_name = preg_split("/\s+/", $_POST['full_name']);

                array_walk($full_name, 'array_walk_trim_callback');

                $full_name = array_filter($full_name);

                if(count($full_name) == 1)
                {
                    $postFields["first_name"] = $full_name[0];
                }
                elseif(count($full_name))
                {
                    $postFields["first_name"] = array_shift($full_name);
                    $postFields["last_name"] = array_pop($full_name);
                    $postFields["middle_name"] = join(' ', $full_name);
                }
            }

            if(isset($_POST["email_address"]) && $_POST["email_address"])
            {
                $postFields["email_address"] = $_POST["email_address"];

                $postFields['home_number'] = '';
                $postFields['address_line_1'] = '';
                $postFields['address_line_2'] = '';
                $postFields['address_line_3'] = '';
                $postFields['city_name'] = '';
                $postFields['state_code'] = '';
                $postFields['state_name'] = '';
                $postFields['country_code'] = '';
                $postFields['zip_code'] = '';
                $postFields['sub_zip_code'] = '';
                $postFields['home_number'] = '';
                $postFields['status'] = 'Do Not Mail';
                $postFields['mail_type'] = 'html';

                $postFields["lists"] = array($ccContactOBJ->apiPath.'/lists/'.$listID);

                $contactXML = $ccContactOBJ->createContactXML(null,$postFields);

                if (!$ccContactOBJ->addSubscriber($contactXML)) {
				/*if ($_SERVER['REMOTE_ADDR'] == '173.164.72.169')
					{
						if ($ccContactOBJ->lastError == 'This contact already exists.')
						{
							echo '<pre>';
							print_r($ccContactOBJ);
							die();
						}
					}*/
                    $return['message'] = '<p class="error_message">Whoops! '.$ccContactOBJ->lastError.'</p>';
                } else {
                    $return['message'] = '<p class="success_message serif">Thank You!</p>';
                    $_POST = array();
					$redirectToThankYou = 1;
                }
            }
            else
            {
                $return['message'] = '<p class="error_message">Whoops! There were some error(s) with your submission. Please fill out the required fields.</p>';
                $return['inline_errors']['email_address'] = 'error';
            }
        } 
        catch (Exception $e) {
            $return['message'] = '<p class="error_message">Whoops! '.$e->getMessage().'</p>';
        }
    }

    //
    // Here we add an area where messages  are displayed (error or success messages).
    //
	if ($thankyouUrl != '')	
	{
		header('location:'.$thankyouUrl);
		exit();
	}

    return $return;
}

?>