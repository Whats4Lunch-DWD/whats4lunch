<?php
// Class that provides methods for working with the form data.
// There should be NOTHING in this file except this class definition.

class TransactionsController {
	private $mapper;
    private $cart_items_mapper;
    
	public function __construct() {
		global $f3;

        $this->mapper = new DB\SQL\Mapper($f3->get('DB'),"transactions");	// create DB query mapper object
        $this->cart_items_mapper = new DB\SQL\Mapper($f3->get('DB'),"cart_items");
    }
    
    public function add($cart) {
        //print_r($cart);
        //die();
        $this->mapper->dry();

        foreach($cart as $cart_key => $cart_value) {
            $this->mapper[$cart_key]=$cart_value;
        }
        $this->mapper["status"]="in_progress";

        print_r($this->mapper);
        die();

        $_SESSION["CART_SESSION"]=null;
        $this->mapper->save();

        return $this->mapper["id"];
    }

    public function getTransaction($id) {
        $transaction = $this->mapper->load(["id=?",$id]);
        return $transaction;
    }

}