<?php 
//including the database connection
include('db.php');
//setting the header to json
header("content-type: application/json");
//getting the request method
$method= $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// function for validating price
function isValidPrice($price) {
    return is_numeric($price) && $price > 0;
}

// function for validating quantity
function isValidQuantity($quantity) {
    return is_numeric($quantity) && $quantity > 0;
}

// function for validating product name
function isValidName($name) {
    return !empty($name);
}

//switch statement to handle the request method
switch($method){
    case 'GET':
        //get method
        $sql = "SELECT * FROM products WHERE 1=1";
        $conditions = array();  // array to hold conditions
    
        if (isset($input['product_id'])) {
            $id = $input['product_id'];
            $conditions[] = "product_id='$id'";  // add condition for product_id
        }

        // Add filters if present
        if (isset($input['product_name'])) {
            $name = $input['product_name'];
            if (isValidName($name)) {
                $conditions[] = "product_name LIKE '%$name%'";  // add condition for product_name
            } else {
                echo json_encode(["response_code" => 0, "response_message" => "Invalid product name"]);
                exit();
            }
        }

        if (!empty($conditions)) {
            // join the conditions with 'AND' and append to the query
            $sql .= " AND " . implode(' AND ', $conditions);
        }
        
        // Pagination
        $limit = isset($input['limit']) ? (int)$input['limit'] : 10; // default limit to 10
        $offset = isset($input['offset']) ? (int)$input['offset'] : 0; // default offset to 0

        $sql .= " LIMIT $limit OFFSET $offset";
    
        // execute the query
        $result = mysqli_query($con, $sql);
    
        // fetch results
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    
        // output results as JSON
        echo json_encode($rows);

        //end of the get method
        break;
    
    
    case 'POST':
        //post method

        //validate the input
        if (!isset($input['product_name']) || !isValidName($input['product_name'])) {
            echo json_encode(["response_code" => 0, "response_message" => "Invalid product name"]);
            exit();
        }

        if (!isset($input['product_price']) || !isValidPrice($input['product_price'])) {
            echo json_encode(["response_code" => 0, "response_message" => "Invalid product price"]);
            exit();
        }
        if (!isset($input['product_quantity']) || !isValidQuantity($input['product_quantity'])) {
            echo json_encode(["response_code" => 0, "response_message" => "Invalid product quantity"]);
            exit();
        }
            //input to the database
            $prod_id = $input['product_id'];
            $name = $input['product_name'];
            $desc = $input['product_desc'];
            $price = $input['product_price'];
            $quantity = $input['product_quantity'];

            //inserting the data into the database
            $sql = "INSERT INTO products VALUES('$prod_id', '$name', '$desc', '$price', '$quantity')";
            $result = mysqli_query($con, $sql);

            //checking if the data has been inserted
        if($result){
            $response_code = 1;
            $response = array('response_code'=>$response_code, 'response_message'=>'Product added successfully');
            $json = json_encode($response);
            echo $json;
        }

        //if the data has not been inserted
        else{
            $response_code = 0;
            $response = array('response_code'=>$response_code, 'response_message'=>'Failed to add user');
            $json = json_encode($response);
        }
        //end of the post method
        break;

    case 'PUT':
        //put method
        //validate the input
        if (!isset($input['product_name']) || !isValidName($input['product_name'])) {
            echo json_encode(["response_code" => 0, "response_message" => "Invalid product name"]);
            exit();
        }

        if (!isset($input['product_price']) || !isValidPrice($input['product_price'])) {
            echo json_encode(["response_code" => 0, "response_message" => "Invalid product price"]);
            exit();
        }
        if (!isset($input['product_quantity']) || !isValidQuantity($input['product_quantity'])) {
            echo json_encode(["response_code" => 0, "response_message" => "Invalid product quantity"]);
            exit();
        }
        //input to the database
        $prod_id = $input['product_id'];
        $name = $input['product_name'];
        $desc = $input['product_desc'];
        $price = $input['product_price'];
        $quantity = $input['product_quantity'];

        //updating the data in the database
        $sql = "UPDATE products SET product_name='$name', product_desc='$desc', product_price='$price', product_quantity='$quantity' WHERE product_id='$prod_id'";
        $result = mysqli_query($con, $sql);
        
        //checking if the data has been updated
        if($result){
            $response_code = 1;
            $response = array('response_code'=>$response_code, 'response_message'=>'Product updated successfully');
            $json = json_encode($response);
            echo $json;
        }
        //if the data has not been updated
        else{
            $response_code = 0;
            $response = array('response_code'=>$response_code, 'response_message'=>'Failed to update product');
            $json = json_encode($response);
        }
        //end of the put method
        break;

    case 'DELETE':
        //delete method
        //input to the database
        $prod_id = $input['product_id'];
        $sql = "DELETE FROM products WHERE product_id='$prod_id'";
        $result = mysqli_query($con, $sql);
        //checking if the data has been deleted
        if($result){
            $response_code = 1;
            $response = array('response_code'=>$response_code, 'response_message'=>'Product deleted successfully');
            $json = json_encode($response);
            echo $json;
        }
        //if the data has not been deleted
        else{
            $response_code = 0;
            $response = array('response_code'=>$response_code, 'response_message'=>'Failed to delete product');
            $json = json_encode($response);
        }
        //end of the delete method
        break;
        //default method
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}
//closing the connection
$con->close();
?>