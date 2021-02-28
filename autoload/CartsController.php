<?php
// Class that provides methods for working with the form data.
// There should be NOTHING in this file except this class definition.

class CartsController {
	private $mapper;
	private $menus_mapper;

	public function __construct() {
		global $f3;
		global $basket;

		$this->mapper = new DB\SQL\Mapper($f3->get('DB'),"carts");	// create DB query mapper object
        $this->menus_mapper = new DB\SQL\Mapper($f3->get('DB'),"menus");	// create DB query mapper object
    }
    
    public function add($id){
        $menu_item = $this->menus_mapper->load(['id=?', $id]);

        print_r($menu_item);
        
        //$cart = $this->addItem($menu_item);

        //print_r($cart);

        die();
    }

    public function addItem($menu_item) {
        foreach ($menu_item as $item_key => $item_value) {
            $basket->set($item_key,$item_value);
        }
        $basket->set("quantity",1);

        $basket->save();
        $basket->reset();
    }

    public function addCart($data) {
		$this->reset();
		$this->mapper->save();									// save new record with these fields
	}

	public function listCarts() {
		$Carts = $this->mapper->find();	
		$total_Carts = count($Carts);
		$r = array("results"=>$Carts,"total_results"=>$total_Carts);
		
		return $r;
	}

	public function getCart($id) {
		$Cart = $this->mapper->load(['id=?', $id]);
		$menu = $this->menus_mapper->find(['Cart_id=?', $id]);
		$total_menu_items = count($menu);

		$Cart_menu = array("Cart"=>$Cart, "menu"=>$menu, "total_menu_items"=>$total_menu_items);
		return $Cart_menu;
	}

	public function updateCart($data) {
		$this->mapper->save();									// save new record with these fields
	}

	public function deleteCart($id) {
		$this->mapper->load(['id=?', $id]);				// load DB record matching the given ID
		$this->mapper->erase();									// delete the DB record
	}

}