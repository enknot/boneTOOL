<?php
/**
 * @author Tone Bone <dev@enknot.com>
 */
namespace bone;

use \App as App;

class F
{

    public static function print_raw($r)
    {
        echo '<pre>'.print_r($r, 1).'</pre>';
    }

    /**
     * Wraps the passed $r in a pre..
     * @param type $r
     * @param type $title
     * @return type
     */
    public static function print_pre($r, $title = NULL)
    {
        return "<pre><b>$title<br/></b>".print_r($r, 1).'</pre>';
    }

    /**
     * Returns an array from $choices containing only the indexes and values of the keys passed in
     * $selecteion
     */
    public static function array_select($selection, $choices)
    {
        return array_intersect_key($selection, array_fill_keys($choices, null));
    }

    /**
     * Wraps a list of array elements with single quotes for group SQL insertion
     * @todo escape all single quotes before wrapping
     * @param type $array
     * @return array
     */
    public static function quoteWrap(&$array)
    {
        foreach ($array as $key => $val) {
            $array[$key] = "'".str_replace('\'', '\\\'', $val)."'";
        }
        return $array;
    }

    /**
     *
     * @param array $required
     * @param array $search
     * @return boolean
     */
    public static function array_keys_exist(array $required, array $search)
    {
        $req_count = count($required);
        return (count(array_intersect_key(array_flip($required), $search)) === count($required));
    }

    /**
     * Turns an ordinary associative array into one filled with paramaters.
     * @param array $array
     * @return array
     */
    public static function paramify(Array $array)
    {
        $ret = array();

        foreach ($array as $key => $value) {
            $ret[':'.$key] = $value;
        }

        return $ret;
    }

    public static function deramifiy(Array $array)
    {
        $ret = array();
        foreach ($array as $key => $value) {
            $key       = ($key[0] == ':') ? str_replace(':', '', $key) : $key;
            $ret[$key] = $value;
        }

        return $ret;
    }

    /**
     * Sorts the objects in reverse order base on an order element
     * @param type $a
     * @param type $b
     * @return type
     */
    public static function reverseOrderSort($a, $b)
    {
        if (is_array($a)) {
            return $b['order'] - $a['order'];
        } else {
            return $b->order - $a->order;
        }
    }

    /**
     * Sorts the objects in order base on an order element
     * @param type $a
     * @param type $b
     * @return type
     */
    public static function orderSort($a, $b)
    {
        if (is_array($a)) {
            return $a['order'] - $b['order'];
        } else {
            return $a->order - $b->order;
        }
    }
}