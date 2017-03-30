<?php namespace CodeIgniter;

class Entity
{
	/**
	 * Takes an array of key/value pairs and sets them as
	 * class properties, using any `setCamelCasedProperty()` methods
	 * that may or may not exist.
	 *
	 * @param array $data
	 */
	public function fill(array $data)
	{
		foreach ($data as $key => $value)
		{
			$method = 'set'.str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));

			if (method_exists($this, $method))
			{
				$this->$method($value);
			}
			elseif (property_exists($this, $key))
			{
				$this->$key = $value;
			}
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Magic method to allow retrieval of protected and private
	 * class properties either by their name, or through a `getCamelCasedProperty()`
	 * method.
	 *
	 * Examples:
	 *
	 *      $p = $this->my_property
	 *      $p = $this->getMyProperty()
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get(string $key)
	{
		// Convert to CamelCase for the method
		$method = 'get'.str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));

		// if a set* method exists for this key, 
		// use that method to insert this value. 
		if (method_exists($this, $method))
		{
			return $this->$method();
		}

		// Otherwise return the protected property
		// if it exists.
	    if (property_exists($this, $key))
	    {
	    	return $this->$key;
	    }
	}

	//--------------------------------------------------------------------

	/**
	 * Magic method to all protected/private class properties to be easily set,
	 * either through a direct access or a `setCamelCasedProperty()` method.
	 *
	 * Examples:
	 *
	 *      $this->my_property = $p;
	 *      $this->setMyProperty() = $p;
	 *
	 * @param string $key
	 * @param null   $value
	 *
	 * @return $this
	 */
	public function __set(string $key, $value = null)
	{
		// if a set* method exists for this key, 
		// use that method to insert this value. 
		$method = 'set'.str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));
		if (method_exists($this, $method))
		{
			$this->$method($value);

			return $this;
		}
		elseif (property_exists($this, $key))
		{
			$this->$key = $value;

			return $this;
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Unsets a protected/private class property. Sets the value to null.
	 * However, if there was a default value for the parent class, this
	 * attribute will be reset to that default value.
	 *
	 * @param string $key
	 */
	public function __unset(string $key)
	{
		$this->$key = null;

		// Get the class' original default value for this property
		// so we can reset it to the original value.
		$reflectionClass = new \ReflectionClass($this);
		$defaultProperties = $reflectionClass->getDefaultProperties();

		if (isset($defaultProperties[$key]))
		{
			$this->$key = $defaultProperties[$key];
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Returns true if a property exists names $key, or a getter method
	 * exists named like for __get().
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function __isset(string $key): bool
	{
		$value = $this->$key;

		return ! is_null($value);
	}

	//--------------------------------------------------------------------

}
