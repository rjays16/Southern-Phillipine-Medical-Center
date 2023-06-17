<?php
/**
 * FreeFormModel.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 */
/**
 * CFormModel lazy version.
 *
 * You dont have to declare any attributes/rules/labels etc. Jjust plug into
 * your favorite CActiveForm and form away. VERY UNSAFE! Never use this for
 * storing things in the database. DO NOT ABUSE!
 */
class FreeFormModel extends CFormModel
{
    /**
     * @var array Data black hole
     */
    protected $data = array();
    /**
     * PHP getter magic method.
     *
     * @param string $name property name
     * @return mixed property value
     */
    public function __get($name)
    {
        return @$this->data[$name];
    }
    /**
     * PHP setter magic method.
     *
     * @param string $name property name
     * @param mixed $value property value
     */
    public function __set($name,$value)
    {
        $this->data[$name] = $value;
    }
    /**
     * Checks if a property value is null. Hint: it's not.
     *
     */
    public function __isset($name)
    {
        return true;
    }
    /**
     * Sets the attribute values in a massive way. Removes all safe-attribute
     * checking as this model is meant to be used dynamically.
     *
     * @param array $values attribute values (name=>value) to be set.
     * @param boolean $safeOnly whether the assignments should only be done to the safe attributes.
     * A safe attribute is one that is associated with a validation rule in the current {@link scenario}.
     * @see getSafeAttributeNames
     * @see attributeNames
     */
    public function setAttributes($values,$safeOnly=true)
    {
        if(!is_array($values))
            return;
        foreach($values as $name=>$value)
        {
            $this->$name=$value;
        }
    }
    /**
     * Returns the list of attribute names based on the contents of the
     * {@link $data} property.
     *
     * @return array list of attribute names. Defaults to all public properties of the class.
     */
    public function attributeNames()
    {
        return array_keys($this->data);
    }
}