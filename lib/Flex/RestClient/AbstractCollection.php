<?php

namespace Flex\RestClient;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

Abstract Class AbstractCollection implements ArrayAccess, Countable, IteratorAggregate {

	protected $collection = array();

	/**
	 * Sets an element at the end of the collection
	 *
	 * @param string|integer $key   The key/index of the element to set.
	 * @param mixed          $element element
	 */
	public function add($element) {
		$this->collection[] = $element;
	}

	/**
	 * Sets an element in the collection at the specified key/index.
	 *
	 * @param string|integer $key   The key/index of the element to set.
	 * @param mixed          $element element
	 */
	public function set($key, $element) {
		$this->collection[$key] = $element;
	}

	/**
	 * Gets the element at the specified key/index.
	 *
	 * @param string|integer $key The key/index of the element to retrieve.
	 *
	 * @return mixed
	 */
	public function get($key) {
		if (isset($this->collection[$key])) {
			return $this->collection[$key];
		}
		return null;
	}

	/**
	 * Remove an element in the collection at the specified key/index.
	 *
	 * @param string|integer $key   The key/index of the element to set.
	 */
	public function remove($key) {
		if (isset($this->collection[$key])) {
			unset($this->collection[$key]);
		}
	}

	/**
	 * Checks whether the collection contains a element with the specified key/index.
	 *
	 * @param string|integer $key The key/index to check for.
	 *
	 * @return boolean TRUE if the collection contains an element with the specified key/index,
	 *                 FALSE otherwise.
	 */
	public function exists($key) {
		return isset($this->collection[$key]);
	}

	/**
	 * Sets the internal iterator to the first element in the collection and returns this element.
	 *
	 * @return mixed
	 */
	public function first() {
		return reset($this->collection);
	}

	/**
	 * Sets the internal iterator to the last element in the collection and returns this element.
	 *
	 * @return mixed
	 */
	public function last() {
		return end($this->collection);
	}

	/**
	 * Gets the key/index of the element at the current iterator position.
	 *
	 * @return int|string
	 */
	public function key() {
		return key($this->collection);
	}

	/**
	 * Moves the internal iterator position to the next element and returns this element.
	 *
	 * @return mixed
	 */
	public function next() {
		return next($this->collection);
	}

	/**
	 * Gets the element of the collection at the current iterator position.
	 *
	 * @return mixed
	 */
	public function current() {
		return current($this->collection);
	}

	/**
	 * Required by interface ArrayAccess.
	 *
	 * {@inheritDoc}
	 */
	public function offsetExists($offset) {
		return $this->exists($offset);
	}

	/**
	 * Required by interface ArrayAccess.
	 *
	 * {@inheritDoc}
	 */
	public function offsetGet($offset) {
		return $this->get($offset);
	}

	/**
	 * Required by interface ArrayAccess.
	 *
	 * {@inheritDoc}
	 */
	public function offsetSet($offset, $value) {
		return $this->set($offset, $value);
	}

	/**
	 * Required by interface ArrayAccess.
	 *
	 * {@inheritDoc}
	 */
	public function offsetUnset($offset) {
		return $this->remove($offset);
	}

	/**
	 * Required by interface Countable.
	 *
	 * {@inheritDoc}
	 */
	public function count() {
		return count($this->collection);
	}

	/**
	 * Required by interface IteratorAggregate.
	 */
	public function getIterator() {
		return new ArrayIterator($this->collection);
	}

}
