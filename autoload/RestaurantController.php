<?php
// Class that provides methods for working with the form data.
// There should be NOTHING in this file except this class definition.

class RestaurantController {
	private $mapper;
	private $menus_mapper;

	public function __construct() {
		global $f3;						// needed for $f3->get()
		$this->mapper = new DB\SQL\Mapper($f3->get('DB'),"restaurants");	// create DB query mapper object
																			// for the "restaurants" table
		$this->menus_mapper = new DB\SQL\Mapper($f3->get('DB'),"menus");
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

	public function findRestaurantsBySearch($options) {
		if ($options["choicestyle"]=="3choices") {
			$fields = "dish_name, diet, allergen, restaurant_id";
			$criteria = "dish_name like '%".$options["query"]."%' or diet like '%".$options["diet"]."%' or allergen like '%".$options["allergy"]."%'";
			$dishes = $this->menus_mapper->select($fields,$criteria,array("group"=>"restaurant_id","limit"=>3));
			print_r($dishes);
			$list = $this->mapper->find();
		} else {
			$criteria = "restaurant_id like '%%' and (dish_name like '%".$options["query"]."%' or diet like '%".$options["diet"]."%' or allergen like '%".$options["allergy"]."%')";
			$dishes = $this->menus_mapper->find($criteria,array("group"=>"restaurant_id","limit"=>1));
		}
		return $list;
	}

	public function updateRestaurant($data) {
		$this->mapper->save();									// save new record with these fields
	}

	public function deleteRestaurant($id) {
		$this->mapper->load(['id=?', $id]);				// load DB record matching the given ID
		$this->mapper->erase();									// delete the DB record
	}

}