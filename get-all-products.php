<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__.'/classes/Database.php';
require __DIR__.'/middlewares/Auth.php';

$allHeaders = getallheaders();
$db_connection = new Database();
$conn = $db_connection->dbConnection();
$auth = new Auth($conn,$allHeaders);


if(isset($_GET['productGtin']))
{
    $product_Gtin = filter_var($_GET['productGtin'], FILTER_VALIDATE_INT,[
        'options' => [
            'default' => 'all_products',
            'min_range' => 1
        ]
    ]);
}
else{
    $product_Gtin = 'all_products';
}


$sql = "SELECT * FROM `products`"; 

$stmt = $conn->prepare($sql);

$stmt->execute();

//CHECK IF THERE ARE PRODUCTS IN DB
if($stmt->rowCount() > 0){
    // CREATE PRODUCTS ARRAY
    $products_array = [];
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        
        $product_data = [
            'id' => $row['id'],
            'productGtin' => $row['productGtin'],
            'productName' => $row['productName'],
            'productWeight' => $row['productWeight'],
            'productKcal' => $row['productKcal'],
            'productCarbs' => $row['productCarbs']

        ];
        // PUSH PRODUCT DATA IN OUR $products_array ARRAY
        array_push($products_array, $product_data);
    }
    //SHOW PRODUCTS IN JSON FORMAT
    echo json_encode($products_array);
 

}
else{
    //IF THER IS NO PRODUCTS IN DATABASE
    echo json_encode(['message'=>'No products found']);
}
?>
