<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Basic Sprig_Field implementation.
 *
 * @package	   Sprig
 * @author	   Woody Gilk
 * @copyright  (c) 2009 Woody Gilk
 * @license	   MIT
 */
abstract class Sprig_Field {

	/**
	 * @var bool Allow `empty()` values to be used. Default is `FALSE`.
	 */
	public $empty = FALSE;

	/**
	 * @var bool A primary key field. Multiple primary keys (composite key) can be specified. Default is `FALSE`.
	 */
	public $primary = FALSE;

	/**
	 * @var bool This field must have a unique value within the model table. Default is `FALSE`.
	 */
	public $unique = FALSE;

	/**
	 * @var bool Convert all `empty()` values to `NULL`. Default is `FALSE`.
	 */
	public $null = FALSE;

	/**
	 * @var bool Show the field in forms. Default is `TRUE`.
	 */
	public $editable = TRUE;

	/**
	 * @var string Default value for this field. Default is `''` (an empty string).
	 */
	public $default = '';

	/**
	 * @var array Limit the value of this field to an array of choices. This will change the form input into a select list. No default value.
	 */
	public $choices;

	/**
	 * @var string Database column name for this field. Default will be the same as the field name,
	 * except for foreign keys, which will use the field name with `_id` appended.
	 * In the case of HasMany fields, this value is the column name that contains the
	 * foreign key value.
	 */
	public $column;

	/**
	 * @var string Human readable label. Default will be the field name converted with `Inflector::humanize()`.
	 */
	public $label;

	/**
	 * @var string Description of the field. Default is `''` (an empty string).
	 */
	public $description = '';

	 /**
	 * @var array {@link HTML} html attribute for the field.
	 */
	public $attributes = NULL;

	/**
	 * @var bool The column is present in the database table. Default: TRUE
	 */
	public $in_db = TRUE;

	/**
	 * @var array {@link Validate} filters for this field.
	 */
	public $filters = array();

	/**
	 * @var array {@link Validate} rules for this field.
	 */
	public $rules = array();

	/**
	 * @var array {@link Validate} callbacks for this field.
	 */
	public $callbacks = array();

	public function __construct(array $options = NULL)
	{
		if ( ! empty($options))
		{
			$options = array_intersect_key($options, get_object_vars($this));

			foreach ($options as $key => $value)
			{
				$this->$key = $value;
			}
		}
	}

	public function value($value)
	{
		if ($this->null AND empty($value))
		{
			// Empty values are converted to NULLs
			$value = NULL;
		}

		return $value;
	}

	public function verbose($value)
	{
		$value = $this->value($value);

		return (string) isset($this->choices[$value]) ? $this->choices[$value] : $value;
	}

	public function input($name, $value, array $attr = NULL)
	{
		if (is_array($this->choices))
		{
			return Form::select($name, $this->choices, $this->value($value), $attr);
		}
		else
		{
			return Form::input($name, $this->verbose($value), $attr);
		}
	}

	public function label($name, array $attr = NULL)
	{
		if (is_array($this->label))
		{
			if( isset($this->label[2]))
			{
				$attr = $this->label[2];
			}
			if( isset($this->label[1]) AND is_bool($this->label[1]))
			{
				$autoformat = $this->label[1];
			}
			else
			{
				$autoformat = TRUE;
			}
			if ($autoformat)
			{
				return Form::label($name, html::chars(UTF8::ucwords($this->label[0])), $attr);
			}
			else
			{
				return Form::label($name, $this->label[0], $attr);
			}
		}
		else
		{
			return Form::label($name, UTF8::ucwords($this->label), $attr);
		}
	}

} // End Sprig_Field