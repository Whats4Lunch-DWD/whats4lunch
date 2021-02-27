<?php

  /////////////////////////////////////
 // index.php for Whats4Lunch app //
/////////////////////////////////////

// Create f3 object then set various global properties of it
// These are available to the routing code below, but also to any
// classes defined in autoloaded definitions

$f3 = require('../../AboveWebRoot/fatfree-master-3.7/lib/base.php');

// autoload Controller class(es) and anything hidden above web root, e.g. DB stuff
$f3->set('AUTOLOAD','autoload/;../../AboveWebRoot/autoload/');

$db = DatabaseConnection::connect();		// defined as autoloaded class in AboveWebRoot/autoload/
$f3->set('DB', $db);
$f3->get('DB')->exec("set sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");

$f3->set('DEBUG',3);		// set maximum debug level
$f3->set('UI','ui/');		// folder for View templates


  /////////////////////////////////////////////
 // Simple Example URL application routings //
/////////////////////////////////////////////

//home page (index.html) -- actually just shows form entry page with a different title
$f3->route('GET /',
  function ($f3) {
    $f3->set('html_title','Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','home.html');
    echo Template::instance()->render('layout.html');
  }
);

// search resto
$f3->route('POST /',
  function ($f3) {
    $query = $f3->get('POST');
    $f3->set('query',$query);

    //print_r($query);

    if ($query["choicestyle"]=="3choices") {
      
      $criteria = "dish_name like '%".$query["query"]."%' and diet like '%".$query["diet"]."%'";
      if ($query["allergy"]!="") {
        $criteria .= "and allergen not like '%".$query["allergy"]."%'";
      }
      $sql = "select distinct restaurant_id, restaurant_name, restaurants.image from hazrulaz_whats4lunch.menus inner join hazrulaz_whats4lunch.restaurants on menus.restaurant_id=restaurants.id where ".$criteria;
      //echo $sql;
      $f3->set('results',$f3->get('DB')->exec($sql));

    } else {
      
      $criteria = "(dish_name like '%".$query["query"]."%' and diet like '%".$query["diet"]."%'";
      if ($query["allergy"]!="") {
        $criteria .= "and allergen not like '%".$query["allergy"]."%')";
      } else {
        $criteria .= ")";
      }
      $count_sql = "select count(distinct restaurant_id) as max_records from hazrulaz_whats4lunch.menus inner join hazrulaz_whats4lunch.restaurants on menus.restaurant_id=restaurants.id where ".$criteria;
      //echo $sql;
      $f3->set('sum_of_records',$f3->get('DB')->exec($count_sql));
      $max_records = $f3->get('sum_of_records');
      if ($max_records["0"]["max_records"]<1) {
        $max = 1;
      } else {
        $max = $max_records["0"]["max_records"];
      }
      //print_r($max_records["0"]["max_records"]);
      
      $criteria .= " and restaurants.id=".random_int(1,$max);
      $sql = "select distinct restaurant_id, restaurant_name, restaurants.image from hazrulaz_whats4lunch.menus inner join hazrulaz_whats4lunch.restaurants on menus.restaurant_id=restaurants.id where ".$criteria;
      $f3->set('results',$f3->get('DB')->exec($sql));
    }
    $f3->set('html_title','Restaurant - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','restaurants/search_response.html');
    echo Template::instance()->render('layout.html');
  }
);

$f3->route('GET /about',
  function ($f3) {
    $f3->set('html_title','About - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','about.html');
    echo Template::instance()->render('layout.html');
  }
);

$f3->route('GET /restaurants',
  function ($f3) {
    $controller = new RestaurantController;
    $data = $controller->listRestaurants();
    $f3->set("records", $data);
    $f3->set('html_title','Restaurant - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','restaurants/list.html');
    echo Template::instance()->render('layout.html');
  }
);

// Show the Add restaurants page.
$f3->route('GET /restaurants/add',
  function ($f3) {
    $f3->set('html_title','Restaurant - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','restaurants/add.html');
    echo Template::instance()->render('layout.html');
  }
);

// Todo: modify the comment to add the id value in getRestaurant
// Show the Show restaurants Page
$f3->route('GET /restaurants/show/@id',
  function ($f3,$args) {
    $controller = new RestaurantController;
    //print_r($args);
    $data = $controller->getRestaurant($args['id']);
    $f3->set('result',$data);
    //print_r($data);
    $f3->set('html_title','Restaurant - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','restaurants/show.html');
    echo Template::instance()->render('layout.html');
  }
);

// Show the Update restaurants page.
$f3->route('GET /restaurants/update',
  function ($f3) {
    $controller = new RestaurantController;
    $data = $controller->getRestaurant();
    $f3->set('html_title','Restaurant - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','restaurants/add.html');
    echo Template::instance()->render('layout.html');
  }
);

// Show the Delete restaurants page.
$f3->route('GET /restaurants/delete',
  function ($f3) {
    $controller = new RestaurantController;
    $data = $controller->getRestaurant();
    $f3->set('html_title','Restaurant - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','restaurants/delete.html');
    echo Template::instance()->render('layout.html');
  }
);

// Add a new restaurants
$f3->route('POST /restaurants',
  function ($f3) {
    $controller = new RestaurantController;
    $controller->addRestaurant($formdata);
    $f3->set('formData',$formdata);		// set info in F3 variable for access in response template
    $f3->set('html_title','Restaurant - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','restaurants/add_response.html');
    echo Template::instance()->render('layout.html');
  }
);

// Update an existing restaurants
$f3->route('PUT /restaurants',
  function ($f3) {
    $controller = new RestaurantController;
    $controller->updateRestaurant($formdata);
    $f3->set('formData',$formdata);		// set info in F3 variable for access in response template
    $f3->reroute('/restaurants');		// will show edited data (GET route)
  }
);

// Delete an existing restaurants
$f3->route('DELETE /restaurants',
  function ($f3) {
    $controller = new RestaurantController;
    $controller->deleteRestaurant($f3->get('POST.toDelete'));	// in this case, delete selected data record
    $f3->reroute('/restaurants');		// will show edited data (GET route)
  }
);

$f3->route('GET /menus',
  function ($f3) {
    $f3->set('html_title','Menus - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','menus/list.html');
    echo Template::instance()->render('layout.html');
  }
);

$f3->route('GET /cart',
  function ($f3) {
    $f3->set('html_title','Cart - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','menus/cart.html');
    echo Template::instance()->render('layout.html');
  }
);

/*
// Todo. Create cartscontroller and update the front controller.
// Show the List Carts page.
$f3->route('GET /carts',
  function ($f3) {
    $controller = new CartsController;
    $data = $controller->getData();
    $f3->set('html_title','Carts - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','menus/carts/list.html');
    echo Template::instance()->render('layout.html');
  }
);

// Show the Add Cart page.
$f3->route('GET /carts/add',
  function ($f3) {
    $controller = new CartsController;
    $data = $controller->getData();
    $f3->set('html_title','Carts - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','menus/carts/add.html');
    echo Template::instance()->render('layout.html');
  }
);

// Show the Show Cart Page
$f3->route('GET /carts/show',
  function ($f3) {
    $controller = new CartsController;
    $data = $controller->getData();
    $f3->set('html_title','Carts - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','menus/carts/show.html');
    echo Template::instance()->render('layout.html');
  }
);

// Show the Update Cart page.
$f3->route('GET /carts/update',
  function ($f3) {
    $controller = new CartsController;
    $data = $controller->getData();
    $f3->set('html_title','Carts - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','menus/carts/add.html');
    echo Template::instance()->render('layout.html');
  }
);

// Show the Delete Cart page.
$f3->route('GET /carts/delete',
  function ($f3) {
    $controller = new CartsController;
    $data = $controller->getData();
    $f3->set('html_title','Carts - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','menus/carts/delete.html');
    echo Template::instance()->render('layout.html');
  }
);

// Add a new cart
$f3->route('POST /carts',
  function ($f3) {
    $controller = new CartsController;
    $controller->addCart($formdata);
    $f3->set('formData',$formdata);		// set info in F3 variable for access in response template
    $f3->set('html_title','Carts - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','menus/carts/add_response.html');
    echo Template::instance()->render('layout.html');
  }
);

// Update an existing cart
$f3->route('PUT /carts',
  function ($f3) {
    $controller = new CartsController;
    $controller->updateCart($formdata);
    $f3->set('formData',$formdata);		// set info in F3 variable for access in response template
    $f3->reroute('/carts');		// will show edited data (GET route)
  }
);

// Delete an existing cart
$f3->route('DELETE /carts',
  function ($f3) {
    $controller = new CartsController;
    $controller->deleteCart($f3->get('POST.toDelete'));	// in this case, delete selected data record
    $f3->reroute('/carts');		// will show edited data (GET route)
  }
);

$f3->route('GET /cart_items',
  function ($f3) {
    $f3->set('html_title','Cart Items - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','menus/cart_items/list.html');
    echo Template::instance()->render('layout.html');
  }
);
*/


$f3->route('GET /sign-in',
  function ($f3) {
    $f3->set('html_title','Sign In - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','sign-in.html');
    echo Template::instance()->render('layout.html');
  }
);

$f3->route('GET /cart',
  function ($f3) {
    $f3->set('html_title','Cart - Whats4Lunch - The World\'s easiest Food Delivery for people with diets and allergies');
    $f3->set('content','cart.html');
    echo Template::instance()->render('layout.html');
  }
);

// When using GET, provide a form for the user to upload an image via the file input type
$f3->route('GET /simpleform',
  function($f3) {
    $f3->set('html_title','Simple Input Form');
    $f3->set('content','simpleform.html');
    echo template::instance()->render('layout.html');
  }
);

// When using POST (e.g.  form is submitted), invoke the controller, which will process
// any data then return info we want to display. We display
// the info here via the response.html template
$f3->route('POST /simpleform',
  function($f3) {
	$formdata = array();			// array to pass on the entered data in
	$formdata["name"] = $f3->get('POST.name');			// whatever was called "name" on the form
	$formdata["colour"] = $f3->get('POST.colour');		// whatever was called "colour" on the form
  $formdata["pet"] = $f3->get('POST.pet');

  	$controller = new SimpleController;
    $controller->putIntoDatabase($formdata);

	$f3->set('formData',$formdata);		// set info in F3 variable for access in response template

    $f3->set('html_title','Simple Example Response');
	$f3->set('content','response.html');
	echo template::instance()->render('layout.html');
  }
);

$f3->route('GET /dataView',
  function($f3) {
  	$controller = new SimpleController;
    $alldata = $controller->getData();

    $f3->set("dbData", $alldata);
    $f3->set('html_title','Viewing the data');
    $f3->set('content','dataView.html');
    echo template::instance()->render('layout.html');
  }
);

$f3->route('GET /editView',				// exactly the same as dataView, apart from the template used
  function($f3) {
  	$controller = new SimpleController;
    $alldata = $controller->getData();

    $f3->set("dbData", $alldata);
    $f3->set('html_title','Viewing the data');
    $f3->set('content','editView.html');
    echo template::instance()->render('layout.html');
  }
);

$f3->route('POST /editView',		// this is used when the form is submitted, i.e. method is POST
  function($f3) {
  	$controller = new SimpleController;
    $controller->deleteFromDatabase($f3->get('POST.toDelete'));		// in this case, delete selected data record

	$f3->reroute('/editView');  }		// will show edited data (GET route)
);


  ////////////////////////
 // Run the F3 engine //
////////////////////////

$f3->run();

?>
