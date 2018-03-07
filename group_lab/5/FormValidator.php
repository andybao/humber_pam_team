<?php
/*
 * This class is developed for the lab 5 - Validation Library of course 5202
 * Team members:
 * - Pam Gao [pamela.gao@hotmail.com]
 * - Kevin Wang [kevin.ztwang@gmail.com]
 * - Serena Liao [serenaliaojc@gmail.com]
 * - Uffa Butt [uffa.butt@gmail.com]
 * - Andy Bao [wenyu.bao@gmail.com]
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