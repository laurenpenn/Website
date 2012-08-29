<?php

/**
 * A convenience object for storing information about a flashvar.
 * @file Class definition for FlashVar
 */
class FlashVar
{
  const FIELD = "field";
  const SELECT = "select";

  private $name;
  private $default_value;
  private $description;
  private $values;
  private $type;
  private $group;

  /**
   * Constructor.
   * @param string $nm The name of the flashvar
   * @param string $def The default value for the flashvar
   * @param string $desc The description of what the flashvar does
   * @param array $val The possible values if any the flashvar can take
   * @param string $typ The field type associated with this flashvar
   * @param string $grp The parent group this flashvar belongs to
   */
  function __construct($nm = "", $def = "", $desc = "", $val = array(), $typ = FlashVar::FIELD, $grp = "default") {
    $this->name = $nm;
    $this->default_value = $def;
    $this->description = $desc;
    $this->values = $val;
    $this->type = $typ;
    $this->group = $grp;
  }

  /**
   * Get the name of the flashvar.
   * @return string The flashvar name
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set the name of the flashvar.
   * @param string $newName The new name for the flashvar
   */
  public function setName($new_name) {
    $this->name = $new_name;
  }

  /**
   * Get the default value for the flashvar.
   * @return string The default value
   */
  public function getDefaultValue() {
    return $this->default_value;
  }

  /**
   * Set the default value for the flashvar.
   * @param string $newDefault The new default value for the flashvar
   */
  public function setDefaultValue($new_default) {
    $this->default_value = $new_default;
  }

  /**
   * Get the description of the flashvar.
   * @return string The description of the flashvar
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Set the description of the flashvar.
   * @param string $newDesc The new description of the flashvar
   */
  public function setDescription($new_desc) {
    $this->description = $new_desc;
  }

  /**
   * Get the possible values for the flashvar.
   * @return array The array of possible values for the flashvar
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * Set the possible values for the flashvar.
   * @param array $newVal The new array of possible values for the flashvar
   */
  public function setValues($new_val) {
    $this->values = $new_val;
  }

  /**
   * Get the field type of the flashvar.
   * @return string The field type of the flashvar
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Set the field type of the flashvar.
   * @param string $newType The new field type of the flashvar
   */
  public function setType($new_type) {
    $this->type = $new_type;
  }

  /**
   * Get the parent group the flashvar belongs to.
   * @return string The parent group the flashvar belongs to
   */
  public function getGroup() {
    return $this->group;
  }

  /**
   * Set the parenet group the flashvar belongs to.
   * @param string $newGroup The new parent group the flashvar belongs to
   */
  public function setGroup($new_group) {
    $this->group = $new_group;
  }
}

?>
