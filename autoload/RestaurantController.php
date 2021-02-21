<?php
// Class that provides methods for working with the form data.
// There should be NOTHING in this file except this class definition.

class RestaurantController {
	private $mapper;

	public function __construct() {
		global $f3;						// needed for $f3->get()
		$this->mapper = new DB\SQL\Mapper($f3->get('DB'),"restaurants");	// create DB query mapper object
																			// for the "restaurants" table
    }
    
    public function addRestaurant($data) {
		$this->reset();
		$this->mapper->save();									// save new record with these fields
	}

	public function listRestaurants() {
		$restaurants = $this->mapper->find();		
		return $restaurants;
	}

	public function getRestaurant($id) {
		$restaurant = $this->mapper->load(['id=?', $id]);				// load DB record matching the given ID
		return $restaurant;
	}

	public function findRestaurants($options) {
		//$list = $this->mapper->find();
		//return $list;
		return $options;
	}

	public function updateRestaurant($data) {
		$this->mapper->save();									// save new record with these fields
	}

	public function deleteRestaurant($id) {
		$this->mapper->load(['id=?', $id]);				// load DB record matching the given ID
		$this->mapper->erase();									// delete the DB record
	}

}