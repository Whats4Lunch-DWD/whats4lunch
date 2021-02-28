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

        
        echo "cart session: ";
        print_r($cart_session->id); 
        //die();
        

        if (count($cart_session)<1) {
            $this->addCart($_SESSION["CART_SESSION"]);
        } else {
            $cart_items = $this->cart_items_mapper->find(["cart_id=?", $cart_session->id]);
        }
        
        $menu_item = $this->menus_mapper->load(['id=?', $id]);
        //echo "cart session: ".$_SESSION["CART_SESSION"]."<br />";

        if ($cart_items["menu_id"] != $id) {
            foreach ($menu_item as $item_key => $item_value) {
                echo $item_key."=>".$item_value."<br />";
                if ($item_key != "created_at" ) {
                    if ($item_key == "id") {
                        $this->cart_items_mapper["menu_id"]=$item_value;
                    } else {
                        $this->cart_items_mapper[$item_key]=$item_value;
                    }
                }
            }   
        }

        $this->cart_items_mapper["cart_id"] = $cart_session->id;
        $this->cart_items_mapper["quantity"]+=1;

        
        echo "<pre>";
        print_r($this->cart_items_mapper["cart_id"]);
        echo "<br />";
        print_r($this->cart_items_mapper["id"]);
        echo "<br />";
        print_r($this->cart_items_mapper["menu_id"]);
        echo "</pre>";
        

        die();
        

        $this->cart_items_mapper->save();
    }

    public function addCart($data) {
		$this->mapper->cart_session = $data;
        $this->mapper->save();									// save new record with these fields
	}

	public function listCarts() {
		$Carts = $this->mapper->find();	
		$total_Carts = count($Carts);
		$r = array("results"=>$Carts,"total_results"=>$total_Carts);
		
		return $r;
	}

	public function getCart($cart_session) {
		$cart = $this->mapper->load(['cart_session=?', $cart_session]);
		$cart_items = $this->cart_items_mapper->find(['cart_id=?', $cart->id]);
		$total_cart_items = count($cart_items);
        $total_cart_value = 0;
        foreach ($cart_items as $c) {
            $total_cart_value += $c["price"]*$c["quantity"];
        }

		$mycart = array("cart"=>$cart, "cart_items"=>$cart_items, "total_cart_items"=>$total_cart_items, "total_cart_value"=>total_cart_value);
		return $mycart;
	}

	public function updateCart($data) {
		$this->mapper->save();									// save new record with these fields
	}

	public function deleteCart($id) {
		$this->mapper->load(['id=?', $id]);				// load DB record matching the given ID
		$this->mapper->erase();									// delete the DB record
	}

}