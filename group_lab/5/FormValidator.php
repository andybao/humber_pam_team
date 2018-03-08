<?php
/* Project introduction
 * 1. OVERVIEW
 * - This class is developed for the lab 5 - Validation Library of course 5202
 *
 * 2. TEAM MEMBERS
 * - Pam Gao (n01193625)
 * - Kevin Wang (n01255155)
 * - Serena Liao (n01223460)
 * - Uffa Butt (n00359711)
 * - Andy Bao (n01257490)
 *
 * 3. RESPONSIBILITY
 * - All: strategy design, code review, comments
 * - Pam: demo 2, testing
 * - Kevin: public methods, 2 private methods: emptyStringCheck($input_array), regexCheck($input_array)
 * - Serena: all other private methods
 * - Uffa: demo 1, testing, English grammar check
 * - Andy: strategy draft, code style modify
 *
 * 4. TIMELINE
 * - 1 ~ 2 Mar.: Strategy design, responsibility discussion
 * - 3 Mar.: define method(name, parameters, return value)
 * - 4 Mar.: strategy draft (methods and comments), coding
 * - 5 ~ 6 Mar.: coding
 * - 7 Mar.: testing
 */

/* Library introduction
 * 1. OVERVIEW
 *  1.1 This library gets an array from user, then check the array's format and the value's type, length, and format
 *  1.2 This library returns true if value is valid, otherwise, it will return an associative array (array's key is input_value's alias, and its value is an error msg)
 *  1.3 This library returns error by trigger_error() if syntax is wrong
 *  1.4 User can add their own regular expression to do format check
 *  1.5 Next step: develop method to valiate two values are same (TODO)
 *  1.6 Next next step: develop multidimensional array as input parameter (TODO)
 *
 * 2. PRIVATE METHODS
 *  2.1 parameterArrayKeyCheck($input_array)
 *   - Input array is an associative array, its keys are: value, alias, valueType, length, regex (check part4 for details)
 *   - Private method parameterArrayKeyCheck($input_array) will check these keys, and trigger an error if the array format is invalid
 *  2.2 emptyStringCheck($input_array)
 *   - Input parameter is an array ($input_array), check $input_array['value'] is empty string or not
 *   - Return true if string is not empty, otherwise, return error array as 1.2 mentioned
 *  2.3 typeCheck($input_array)
 *   - Input parameter is an array ($input_array), check type of $input_array['value'] based on $input_array['valueType']
 *   - Return true if value's type is valid, otherwise, return error array as 1.2 mentioned
 *   - Only three types are supported: int, float, string
 *   - This method calls regexCheck($input_array) to do int and float type check
 *   - An error will triggered if $input_array['valueType] is not one of three valid types
 *  2.4 lengthCheck($input_array)
 *   - Input parameter is an array ($input_array), check length of $input_array['value'] based on $input_array['length']
 *   - Return true if value's length euqals expectation, otherwise, return error array as 1.2 mentioned
 *   - $input_array['length'] should ba an positive interger, an error will be triggered if it is invalid
 *   - This method checks string only, otherwise, return error array as 1.2 mentioned
 *   - This method only supports equal opterator, use regexCheck($input_array) to handle not euqal situation
 *  2.5 regexCheck($input_array)
 *   - regular expression is used for format check
 *   - Input parameter is an array ($input_array), get regex alias from $input_array['regex'], then find regex form array $regexes by its alias
 *   - Return true if value is valid, otherwise, return error array as 1.2 mentioned
 *   - Trigger an error if regex alias cannot be found in array $regexes
 *
 * 3. PUBLIC METHODS
 *  3.1 __construct(array $userRegexes = array())
 *   - It accepts an associative regex array as an optional parameter
 *   - array's key is regex's alias, and its value is regex
 *   - It calls addNewregexes($userRegexes) to add input array to $regexes
 *  3.2 addNewRegexes($userRegexes)
 *   - addNewregexes($userRegexes) accepts an associative regex array, and add it to $regexes
 *  3.3 oneItemValidate($input_array)
 *   - It accepts an associative array, then call different private method to check the value
 *   - Check part 4 to get array detail
 *
 * 4. USER INPUT ARRAY DETAILS
 *  - 'value': the test value
 *  - 'alias': It used for return error msg, it should be same as the text which is displayed in the HTML
 *  - 'valueTyp': only support int, float, stirng, the library will not check type if this value is false
 *  - 'length': only support a positive interger, the library will not check length if this value is false
 *  - 'regex': only support the alias in the array $regexes, the library will not check format if this value is false
 *
 */

class FormValidator {
    private $regexes = array(
        'price' => '/^[0-9.,]*(([.,][-])|([.,][0-9]{2}))?$/',
        'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
        'post_code' => '/^([a-zA-Z]\d[a-zA-Z])\ {0,1}(\d[a-zA-Z]\d)$/',
        'account' => '/^[a-zA-Z][a-zA-Z0-9_]{4,15}$/',
        'phone' => '/^[0-9]{10,11}$/',
        'integer' => '/^[-]?[0-9,]+$/',
        'float' => '/^[+-]?([0-9]*[.])?[0-9]+$/'
    );
    const EMPTY_VALUE_WARNING_MSG = 'Value cannot be empty';
    const INVALID_VALUE_FORMAT_WARNING_MSG = 'Value format is invalid';
    const INVALID_VALUE_TYPE_WARNING_MSG = 'Following value type is expected: ';
    const INVALID_VALUE_LENGTH_WARNING_MSG = 'Following value length is expected: ';
    const INVALID_LENGTH_VALUE_WARNING_MSG = 'Numbers are not supported by "lengthCheck": ';
    const UNSUPPORTED_VALUE_TYPE_WARNING_MSG = 'Following value type is not supported: ';
    const UNSUPPORTED_LENGTH_WARNING_MSG = '"lengthCheck" accepts positive integer as length only: ';
    const UNSUPPORTED_REGEX_ALIAS_WARNING_MSG = 'Following regex alias is not supported: ';
    const UNSUPPORTED_PARAMETER_ARRAY_KEY_WARNING_MSG = 'Following parameter array key is not supported: ';
    const PARAMETER_ARRAY_KEY = array('value', 'alias', 'valueType', 'length', 'regex');

    public function __construct(array $userRegexes = array()) {
        if(!empty($userRegexes)) {
            $this->addNewRegexes($userRegexes);
        }
    }

    public function addNewRegexes($userRegexes) {
        $this->regexes += $userRegexes;
    }

    public function oneItemValidate($input_array) {
        if ($this->parameterArrayKeyCheck($input_array)) {
            $valueTypeFlag = $input_array['valueType'];
            $lengthFlag = $input_array['length'];
            $regexFlag = $input_array['regex'];
            if ($valueTypeFlag !== false) {
                $valueTypeCheckResult = $this->typeCheck($input_array);
                if ($valueTypeCheckResult !== true) {
                    return $valueTypeCheckResult;
                }
            }
            if ($lengthFlag !== false) {
                $lengthCheckResult = $this->lengthCheck($input_array);
                if ($lengthCheckResult !== true) {
                    return $lengthCheckResult;
                }
            }
            if ($regexFlag !== false) {
                $regexCheckResult = $this->regexCheck($input_array);
                if ($regexCheckResult !== true) {
                    return $regexCheckResult;
                }
            }
            return true;
        }
    }

    /*
     * array($value, $alias, $valueType, $length, $regex)
     */
    public function testIt($i) {
        //return $this->parameterArrayKeyCheck($i);
        return $this->regexCheck($i);
        /*  $testArray = $this->generateReturnArray('test', 'xxx');
           foreach($testArray as $k => $v) {
           print $k . ' : ' . $v . PHP_EOL;
           }
           foreach($this->regexes as $name => $regex) {
           print $name . ' : ' . $regex . PHP_EOL;
           }*/
    }

    private function typeCheck($input_array) {
        $emptyStringCheckResult = $this->emptyStringCheck($input_array);
        if ($emptyStringCheckResult === true) {
            $i = $input_array['value'];
            $t = $input_array['valueType'];
            switch($t){
                case 'int':
                    $input_array['regex'] = 'integer';
                    if($this->regexCheck($input_array) !== true) {
                        return $this->generateReturnArray($input_array['alias'], self::INVALID_VALUE_TYPE_WARNING_MSG . $t);
                    }
                    break;
                case 'float':
                    $input_array['regex'] = 'float';
                    if($this->regexCheck($input_array) !== true) {
                        return $this->generateReturnArray($input_array['alias'], self::INVALID_VALUE_TYPE_WARNING_MSG . $t);
                    }
                    break;
                case 'string':
                    if(is_numeric($i)) {
                        return $this->generateReturnArray($input_array['alias'], self::INVALID_VALUE_TYPE_WARNING_MSG . $t);
                    }
                    break;
                default:
                    trigger_error(self::UNSUPPORTED_VALUE_TYPE_WARNING_MSG . $t);
                    return false;
                    break;
            }
        } else {
            return $emptyStringCheckResult;
        }
        return true;
    }

    private function lengthCheck($input_array) {
        $l = $input_array['length'];
        $i = $input_array['value'];
        $emptyStringCheckResult = $this->emptyStringCheck($input_array);
        if ($emptyStringCheckResult === true) {
            if (!is_numeric($i)) {
                if (is_int($l) && $l > 0) {
                    if (strlen($i) != $l) {
                        return $this->generateReturnArray($input_array['alias'], self::INVALID_VALUE_LENGTH_WARNING_MSG . $l);
                    }
                } else {
                    trigger_error(self::UNSUPPORTED_LENGTH_WARNING_MSG . $l);
                    return false;
                }
            } else {
                return $this->generateReturnArray($input_array['alias'], self::INVALID_LENGTH_VALUE_WARNING_MSG . $i);
            }
        } else {
            return $emptyStringCheckResult;
        }
        return true;
    }

    private function regexCheck($input_array) {
        $i = $input_array['value'];
        $regex_alias = $input_array['regex'];
        if (array_key_exists($regex_alias, $this->regexes)) {
            $r = $this->regexes[$regex_alias];
            $emptyStringCheckResult = $this->emptyStringCheck($input_array);
            if ($emptyStringCheckResult === true) {
                if (preg_match($r, $i) !== 1) {
                    return $this->generateReturnArray($input_array['alias'], self::INVALID_VALUE_FORMAT_WARNING_MSG);
                }
            } else {
                return $emptyStringCheckResult;
            }
        } else {
            trigger_error(self::UNSUPPORTED_REGEX_ALIAS_WARNING_MSG . $regex_alias);
            return false;
        }
        return true;
    }

    private function generateReturnArray($k, $v) {
        return array($k . '' => $v);
    }

    private function trimInputValue($i) {
        $inputData = trim($i);
        $inputData = stripslashes($inputData);
        $inputData = htmlspecialchars($inputData);
        return $inputData;
    }

    private function emptyStringCheck($input_array) {
        $i = $input_array['value'];
        $testDate = $this->trimInputValue($i);
        if ($testDate === '') {
            return $this->generateReturnArray($input_array['alias'], self::EMPTY_VALUE_WARNING_MSG);
        }
        return true;
    }

    private function parameterArrayKeyCheck($input_array) {
        foreach ($input_array as $k => $v) {
            if (!in_array($k, self::PARAMETER_ARRAY_KEY, true)) {
                trigger_error(self::UNSUPPORTED_PARAMETER_ARRAY_KEY_WARNING_MSG . $k);
                return false;
            }
        }
        return true;
    }
}

$testArray = array (
    'value' => 'wenyu.bao@gmail.com',
    'alias' => 'email',
    'valueType' => 'string',
    'length' => false,
    'regex' => 'email'
);

$validator = new FormValidator();
$test = $validator->oneItemValidate($testArray);
//$testArray = $validator->testIt($testArray);
//print_r ($test);
//print $test . PHP_EOL;
?>