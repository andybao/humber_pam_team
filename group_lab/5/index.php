<?php
include_once('FormValidator.php');
$validator = new FormValidator();

if(isset($_POST['email_submit'])) {
    $demo_1_info = '';
    $email = $_POST['user_email'];
    $input_array = array(
        'value' => $email,
        'alias' => 'Email',
        'valueType' => 'string',
        'length' => false,
        'regex' => 'email'
    );
    $validate_result = $validator->oneItemValidate($input_array);
    if ($validate_result === true) {
        $demo_1_info = '<span style="color:green">SUCCESS! Your email is: ' . $email . '</span>';
    } else {
        foreach($validate_result as $k => $v) {
            $demo_1_info = '<span style="color:red">' . $k . ' : ' . $v . '</span>';
        }
    }
}
if(isset($_POST['int_value_submit'])) {
    $demo_1_info = '';
    $input_int = $_POST['user_int_value'];
    $input_array = array(
        'value' => $input_int,
        'alias' => 'Integer',
        'valueType' => 'int',
        'length' => false,
        'regex' => false
    );
    $validate_result = $validator->oneItemValidate($input_array);
    if ($validate_result === true) {
        $demo_1_info = '<span style="color:green">SUCCESS! Your input "' . $input_int .
                       '" is an integer</span>';
    } else {
        foreach($validate_result as $k => $v) {
            $demo_1_info = '<span style="color:red">' . $k . ' : ' . $v . '</span>';
        }
    }
}
if(isset($_POST['string_value_submit'])) {
    $demo_1_info = '';
    $input_string = $_POST['user_string_value'];
    $input_array = array(
        'value' => $input_string,
        'alias' => 'String',
        'valueType' => false,
        'length' => 3,
        'regex' => false
    );
    $validate_result = $validator->oneItemValidate($input_array);
    if ($validate_result === true) {
        $demo_1_info = '<span style="color:green">SUCCESS! Your input "' . $input_string .
                       '" is a valid string</span>';
    } else {
        foreach($validate_result as $k => $v) {
            $demo_1_info = '<span style="color:red">' . $k . ' : ' . $v . '</span>';
        }
    }
}
if(isset($_POST['user_submit'])) {
    $demo_2_info = '';
    $user_account = $_POST['user_account'];
    $user_number = $_POST['user_number'];

    $account_array = array(
        'value' => $user_account,
        'alias' => 'Account',
        'valueType' => 'string',
        'length' => false,
        'regex' => 'account'
    );

    $number_array = array(
        'value' => $user_number,
        'alias' => 'Phone number',
        'valueType' => false,
        'length' => false,
        'regex' => 'phone'
    );

    $account_result = $validator->oneItemValidate($account_array);
    if($account_result === true) {
        $number_result = $validator->oneItemValidate($number_array);
        if($number_result === true) {
            $demo_2_info = '<span style="color:green"> SUCCESS! Your account is: ' . $user_account .
                           ', and your phone number is: ' . $user_number . '</span>';
        } else {
            $user_number = '';
            foreach($number_result as $k => $v) {
                $demo_2_info = '<span style="color:red">' . $k . ' : ' . $v . '</span>';
            }
        }
    } else {
        $user_account = '';
        foreach($account_result as $k => $v) {
            $demo_2_info = '<span style="color:red">' . $k . ' : ' . $v . '</span>';
        }
    }
}
include_once ('ui.php');
?>