<?php

/**
 * Class to implement Easy-To-Use array operations
 * based on LINQ from C# programming language
 *
 * @author Filip Matys
 * @author Jiri Kratochvil
 */
class LINQ
{

    // Array to keep data to work with
    private $Array;
    private $TotalCount;
    private $Page;

    // Temp variable
    private $tmp;

    // Constants for Where clausule comparation
    const IS_GREATER = ">";
    const IS_LESSER = "<";
    const IS_EQUAL = "==";
    const IS_EQUAL_OR_GREATER = ">=";
    const IS_EQUAL_OR_LESSER = "<=";
    const IS_NOT_EQUAL = "!=";

    /**
     * Constructor that needs array to work with. This class
     * represents wrapper object, which can return given array
     * if desired.
     *
     * @param type $Array
     */
    public function __construct(array $Array)
    {
        $this->Array = $Array;
    }

    /**
     * Set total count
     * @param total count
     */
    public function SetTotalCount($totalCount)  {
        $this->TotalCount = $totalCount;
    }

    /**
     * Get total count
     * @return 
     */
    public function GetTotalCount() {
        return $this->TotalCount;
    }

    /**
     * Set page
     * @param page
     */
    public function SetPage($page)  {
        $this->Page = $page;
    }

    /**
     * Get page
     * @return page
     */
    public function GetPage()   {
        return $this->Page;
    }

    /*
     * Return wrapped data as array 
     */
    public function ToList()
    {
        return $this->Array;
    }

    /**
     * Returns number of elements
     */
    public function Count()
    {
        return count($this->Array);
    }

    /**
     * Return true if empty
     * @return type
     */
    public function IsEmpty()
    {
        return ($this->Array == null || empty($this->Array));
    }

    /**
     * Return last element of array
     * @return type
     */
    public function Last()
    {
        return end($this->Array);
    }

    /**
     * Return array of properties by given property
     * name
     *
     * @param string $propertyName
     * @return \LINQ
     */
    public function Select($property)
    {
        // Init variable
        $result = array();

        // Iterate over array
        foreach ($this->Array as $item) {
            $result[] = $item->{$property};
        }

        // Return result
        return new LINQ($result);
    }

    /**
     * Return filtered wrapped list
     *
     * @param string $property
     * @param type $comparison
     * @param type $value
     * @return \LINQ
     */
    public function Where($property, $comparison, $value)
    {
        // Init variable
        $result = array();

        // If is simple array
        if (is_null($property)) {
            foreach ($this->Array as $item) {
                if (eval('return ' . '$item' . $comparison . '$value' . ';')) {
                    $result[] = $item;
                }
            }
        }
        // Else array of objects
        else    {
            foreach ($this->Array as $item) {
                if (eval('return ' . '$item->{$property}' . $comparison . '$value' . ';')) {
                    $result[] = $item;
                }
            }
        }
        
        // Return result
        return new LINQ($result);
    }

    /**
     * Filter by finding substring in given
     * properties
     *
     * @param $value
     * @return LINQ
     */
    public function WhereContainsMultiple($value)
    {
        // Init variable
        $result = array();

        $parameters = func_get_args();
        $properties = array_slice($parameters, 1, count($parameters) - 1);

        // Iterate through each item
        foreach ($this->Array as $item) {
            // Iterate through properties
            foreach ($properties as $property) {
                // Check if property contains string
                if (strpos(strtolower($item->{$property}), strtolower($value)) !== false) {
                    $result[] = $item;
                    break;
                }
            }
        }

        // Return result
        return new LINQ($result);
    }

    /**
     * Filter by prefix for given property
     * @param mixed property
     * @param mixed prefix
     * @return LINQ
     */
    public function WhereStartsWith($property, $prefix)  {
        // Init result
        $result = array();

        // Iterate through each item
        foreach ($this->Array as $item) {
            if (substr($item->{$property}, 0, strlen($prefix)) === $prefix) {
                $result[] = $item;
            }
        }

        // Return result
        return new LINQ($result);
    }
    
    /**
     * Check if given value is in array
     *
     * @param value
     *
     * @return boolean true, if is in array
     */
    public function Contains($value)    {
        foreach ($this->Array as $item) {
            if ($value === $item)
                return true;
        }
        return false;
    }

    /**
     * Get slice of array from given index
     *
     * @param $start
     * @param $number
     *
     * @return array
     */
    public function Slice($start, $number)
    {
        // Get number of array elements
        $arrayCount = count($this->Array);

        // Check start index and if array is empty
        if (empty($this->Array) || $start > ($arrayCount - 1)) {
            return array();
        }

        // Init start index
        $startIndex = $start;

        // Check number of elements and calculate new value if necessary
        if (($arrayCount - $startIndex) >= $number) {
            $elementsCount = $number;
        } else {
            $elementsCount = $arrayCount - $startIndex;
        }

        // Return desired array
        return new LINQ(array_slice($this->Array, $startIndex, $elementsCount));
    }

    /**
     * Get ordered wrapped object by given property
     *
     * @param string $property
     * @return \LINQ
     */
    public function OrderBy($property)
    {
        // Save property for comparation method
        $this->tmp = $property;

        // Apply comparation function
        usort($this->Array, array($this, 'cmp'));

        // Return result
        return new LINQ($this->Array);
    }

    public function OrderByNumber($property)
    {
        // Save property for comparation method
        $this->tmp = $property;

        // Apply comparation function
        usort($this->Array, array($this, 'cmpNumber'));

        // Return result
        return new LINQ($this->Array);
    }

    public function OrderByDate($property)
    {
        // Save property for comparation method
        $this->tmp = $property;

        // Apply comparation function
        usort($this->Array, array($this, 'cmpDate'));

        // Return result
        return new LINQ($this->Array);
    }

    /**
     * Get element at given index
     *
     * @param $index
     * @return null
     */
    public function ElementAt($index)
    {
        // Return null if given index has no value
        if (!isset($this->Array[$index])) {
            return null;
        }

        // Return element at given index
        return $this->Array[$index];
    }
    
    /**
     * Create array of object of given property
     */
    public function GroupBy($property)  {
        $result = array();
        $properties = array();
        $lArray = new LINQ($this->Array);
        // Iterate through array
        foreach ($this->Array as $item) {
            // get property value
            $propertyValue = $item->{$property};
            // Check, if was already processed
            if (!in_array($propertyValue, $properties)) {
                $properties[] = $propertyValue;
                // Get all elements of given property value
                $result[] = $lArray->Where($property, LINQ::IS_EQUAL, $propertyValue)->ToList();
            }
        }
        
        // Return result
        return new LINQ($result);
    }
   
    
    /**
     * Add element to array
     *
     * @param element to be added
     */
    public function Add($element)   {
        $this->Array[] = $element;
    }

    /**
     * Get first item of array, or, if array is null,
     * return null (and avoid errors)
     *
     * @return First item of wrapped array
     */
    public function Single()
    {
        if (is_null($this->Array)) {
            return null;
        } elseif (empty($this->Array)) {
            return null;
        } else {
            return $this->Array[0];
        }

    }

    /**
     * Comparation function
     *
     * @param type $a
     * @param type $b
     * @return type
     */
    public function cmp($a, $b)
    {
        return strcmp($a->{$this->tmp}, $b->{$this->tmp});
    }

    public function cmpNumber($a, $b)
    {

        return $a->{$this->tmp} - $b->{$this->tmp};

    }

    public function cmpDate($a, $b)
    {
        return (strtotime($b->{$this->tmp}) - strtotime($a->{$this->tmp}));

    }
}

?>
