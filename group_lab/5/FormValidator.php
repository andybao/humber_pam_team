<?php
/* Project introduction
 * 1. OVERVIEW
 * - This class is developed for the lab 5 - Validation Library of course 5202
 *
 * 2. TEAM MEMBERS
 * - Pam Gao (n01193625)
 * - Kevin Wang [kevin.ztwang@gmail.com]
 * - Serena Liao [serenaliaojc@gmail.com]
 * - Uffa Butt [uffa.butt@gmail.com]
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

/* Function introduction
 * 1. OVERVIEW
 *  1.1 This library get user input as an array, then check the array's format and the value's type, length, and format
 *  1.2 This library return true if value is valid, otherwise, it will return an associative array (array's key is input_value's alias, and its value is an error msg)
 *  1.3 User can add their own regular expression to do format check
 *  1.4 Next step: develop multidimensional array as input parameter (TODO)
 *
 * 2. PRIVATE METHODS
 *  2.1 Input parameter array format check
 *   - Input array is an associative array, its keys are: value, alias, valueType, length, regex (check part4 for details)
 *   - Private method parameterArrayKeyCheck($input_array) will check these keys, and trigger an error if the array format is invalid
 *  2.2 Empty string check
 *   - Input parameter is an array ($input_array), check $input_array['value'] is empty string or not
 *   - Return true if string is not empty, otherwise, return error array as 1.2 mentioned
 *  2.3 Type check
 *   - Input parameter is an array ($input_array), check type of $input_array['value'] based on $input_array['valueType']
 *   - Return true if value's type is valid, otherwise, return error array as 1.2 mentioned
 *   - Only three types are supported: int, float, string
 *   - An error will triggered if $input_array['valueType] is not one of three valid types
 *  2.4 Length check
 *   - Input parameter is an array ($input_array), check length of $input_array['value'] based on $input_array['length']
 *   - Return true if value's length euqals expectation, otherwise, return error array as 1.2 mentioned
 *   - $input_array['length'] should ba an positive interger, an error will be triggered if it is invalid
 *   - This method checks string only, an error will be triggered if $input_array['valueType] is not a string
 *   - This method only support equal opterator, use regexCheck($input_array) to handle not euqal situation
 *  2.5 Format check
 *   - regular expression is used for format check
 *   - Input parameter is an array ($input_array), get regex alias from $input_array['regex'], then find regex form array $regexes by its alias
 *   - Return true if value is valid, otherwise, return error array as 1.2 mentioned
 *   - Trigger an error if regex alias cannot be found in array $regexes
 *
 * 3. PUBLIC METHODS
 *  3.1 Construct
 *   - It accepts an associative regex array as an optional parameter
 *   - array's key is regex's alias, and its value is regex
 *   - It call addNewregexes($userRegexes) to add input array to $regexes
 *  3.2 Add a regex
 *   - addNewregexes($userRegexes) accepts an associative regex array, and add it to $regexes
 *  3.3 oneitemvalidate
 *   - It accepts an associative array, then call different private method to check the value
 *   - Check part 4 to get array detail
 *
 * 4. Array details
 *  - 'value': the test value
 *  - 'alias': It used for return error msg, it should be same as the text which is displayed in the HTML
 *  - 'valueTyp': only support int, float, stirng, the library will not check type if this value is false
 *  - 'length': only support a positive interger, the library will not check length if this value is false
 *  - 'regex': only support the alias in the array $regexes, the library will not check format if this value is false
 *
 */

class FormValidator {
    private $regexes = array(
        '2decimals' => '/^([1-9][0-9]*)+(.[0-9]{1,2})?$/',
        'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/'
    );
    const EMPTY_VALUE_WARNING_MSG = 'Value cannot be empty';
    const INVALID_VALUE_FORMAT_WARNING_MSG = 'Value format is invalid';
    const INVALID_VALUE_TYPE_WARNING_MSG = 'Following value type is expected: ';
    const INVALID_VALUE_LENGTH_WARNING_MSG = 'Following value length is expected: ';
    const UNSUPPORTED_VALUE_TYPE_WARNING_MSG = 'Following value type is not supported: ';
    const UNSUPPORTED_LENGTH_WARNING_MSG = '"lengthCheck" accepts positive integer as length only: ';
    const UNSUPPORTED_LENGTH_VALUE_WARNING_MSG = 'Numbers are not supported by "lengthCheck": ';
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
                    if(!is_int($i)) {
                        return $this->generateReturnArray($input_array['alias'], self::INVALID_VALUE_TYPE_WARNING_MSG . $t);
                    }
                    break;
                case 'float':
                    if(!is_float($i)) {
                        return $this->generateReturnArray($input_array['alias'], self::INVALID_VALUE_TYPE_WARNING_MSG . $t);
                    }
                    break;
                case 'string':
                    if(!is_string($i)) {
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
                trigger_error(self::UNSUPPORTED_LENGTH_VALUE_WARNING_MSG . $i);
                return false;
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
print_r ($test);
print $test . PHP_EOL;
?>