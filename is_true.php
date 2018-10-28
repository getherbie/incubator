<?php

/**
 * @param $val
 * @param bool|false $return_null
 * @return bool|mixed|null
 * @see http://php.net/manual/de/function.boolval.php#116547
 */
function is_true($val, $return_null = false)
{
    $boolval = (is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool)$val);
    return ($boolval === null && !$return_null ? false : $boolval);
}
