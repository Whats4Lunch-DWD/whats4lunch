<?php
// Class that provides methods for working with the form data.
// There should be NOTHING in this file except this class definition.

class CartsController {
	private $mapper;
    private $cart_items_mapper;
	private $menus_mapper;
    
	public function __construct() {
		global $f3;

        $this->mapper = new DB\SQL\Mapper($f3->get('DB'),"carts");	// create DB query mapper object
        $this->cart_items_mapper = new DB\SQL\Mapper($f3->get('DB'),"cart_items");
        $this->menus_mapper = new DB\SQL\Mapper($f3->get('DB'),"menus");	// create DB query mapper object
    }
    
    public function add($id){
        
        $cart_session = $this->mapper->find(["cart_session=?",$_SESSION["CART_SESSION"]]);
        $cart_items = null;

        if (count($cart_session)<1) {
            $this->addCart($_SESSION["CART_SESSION"]);
        } else {
            $cart_items = $this->cart_items_mapper->find(["cart_id=?", $cart_session->cart_id]);
        }
        
        $menu_item = $this->menus_mapper->load(['id=?', $id]);
        

        echo "cart session: ".$_SESSION["CART_SESSION"]."<br />";

        foreach ($menu_item as $item_key => $item_value) {
            echo $item_key."=>".$item_value."<br />";
        }

        print_r($cart_items);
        
        die();
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