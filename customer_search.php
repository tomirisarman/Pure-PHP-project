<?php

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: customer.php");
    exit;
}

require_once "config.php";

$item="";
$rad_err="";

if( isset( $_POST['add'] ) )
{
    $item=$_POST['add'];
    $sql='Insert into sales (InvID, CID) values (?, ?)';

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ii", $param_InvID, $param_CID);

            $param_InvID=$item;
            $param_CID=$_SESSION["id"]; 

            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                mysqli_stmt_store_result($stmt);
                header("location: customer_purchases.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }        
        }

    //$updating=mysqli_query($link, "Update inventories set Status='purchased' where InvID=".$item."; ");

    mysqli_close($link);
}


?>

<!DOCTYPE html>
<html>
<head>
<title>Customer Interface</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

<style type="text/css">
body{margin-left: 50px;}
td{ padding: 20px;}
.wrapper{ width: 350px; padding: 20px;}
</style>
</head>
<body>
<!--
<div class="row">
<h4>Smart search:</h4></center>
<span>By brand  <input type="radio" name="brand" value="'.$row['BrandName'].'"></span>
<span>By model  <input type="radio" name="brand" value="'.$row['BrandName'].'"></span>
<span>By price  <input type="radio" name="brand" value="'.$row['BrandName'].'"></span>
<span>By brand and price  <input type="radio" name="brand" value="'.$row['BrandName'].'"></span>
<span>By model and price  <input type="radio" name="brand" value="'.$row['BrandName'].'"></span>
</div>
-->

<a href="customer_welcome.php">Go back to your homepage.</a>
<div class="row">
        <form method="post">
            <div class="form-group" style="display: inline;">
                <h4>Brand</h4>

<?php

$result=mysqli_query($link, 'Select BrandName from brands');
while ($row=mysqli_fetch_array($result))
{

    //echo '<option value="'.$row[BrandName].'">'.$row[BrandName].'</option>';
    echo '<span>   '.$row['BrandName'].'  <input type="radio" name="brand" value="'.$row['BrandName'].'"></span>';
}
?>      
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
        </form>
    </div>

</div>
<div class="row">

<?php
    global $brand ;
    if( isset( $_POST['brand'] ) ){
        $brand=$_POST['brand'];
        $res=mysqli_query($link, 'Select ModelName from models right join brands on models.BrandID=brands.BrandID where BrandName="'.$brand.'";');
        echo '<form method="post"><div class="form-group"><h4>Model</h4>';
        while ($row=mysqli_fetch_array($res))
        {
            echo '<span>'.$row['ModelName'].'  <input type="radio" name="model" value="'.$row['ModelName']. '"></span>';
        }

        echo'</div><div class="form-group"><input type="submit" class="btn btn-primary" value="Submit"></div></form>';

        $res=mysqli_query($link, 
        'Select d.InvID, VIN, BrandName, ModelName, Body, Color, MName, DName, d.Price from
        (Select InvID, Price, DName from dealers, inventories where inventories.DID=dealers.DID) as d
        right join
        (Select InvID, inventories.VIN, BrandName, ModelName, Body, Color, MName from  
            (select VIN, BrandName, ModelName, Body, Color, MName from models, vehicles, brands, manufacturers where vehicles.ModelID=models.ModelID and brands.BrandID=models.BrandID and manufacturers.MID=vehicles.MID) as a right join inventories on inventories.VIN=a.VIN) as m
        on d.InvID=m.InvID where d.InvID not in (select InvID from sales) and BrandName="'.$brand.'" ;');
    }

    if( isset( $_POST['model'])  ){
        $model=$_POST['model'];
        $res=mysqli_query($link, 
        'Select d.InvID, VIN, BrandName, ModelName, Body,Color, MName, DName, d.Price from
        (Select InvID, Price, DName from dealers, inventories where inventories.DID=dealers.DID) as d
        right join
        (Select InvID, inventories.VIN, BrandName, ModelName, Body,Color, MName from  
            (select VIN, BrandName, ModelName, Body, Color, MName from models, vehicles, brands, manufacturers where vehicles.ModelID=models.ModelID and brands.BrandID=models.BrandID and manufacturers.MID=vehicles.MID) as a right join inventories on inventories.VIN=a.VIN) as m
        on d.InvID=m.InvID where d.InvID not in (select InvID from sales) and ModelName="'.$model.'";');
    }


        if (!empty($res)){
            echo '<form method="post"><div class="form-group"><table border=1 style="position: absolute;">

              <tr>
                    <td>InvID</td>
                    <td>VIN</td>
                    <td>Brand</td>
                    <td>Model</td>
                    <td>Manufacturer</td>
                    <td>Body</td>
                    <td>Color</td>
                    <td>Dealer</td>
                    <td>Price</td>
                  </tr>

            ';
            while ($row=mysqli_fetch_array($res))
            {
                echo'

                  <tr>
                    <td><input type="radio" name="add" value= '.$row['InvID'].' ></td>
                    <td>'.$row['VIN'].'</td>
                    <td>'.$row['BrandName'].'</td>
                    <td>'.$row['ModelName'].'</td>
                    <td>'.$row['MName'].'</td>
                    <td>'.$row['Body'].'</td>
                    <td>'.$row['Color'].'</td>
                    <td>'.$row['DName'].'</td>
                    <td>'.$row['Price'].'</td>
                  </tr>
                ';
            }
            echo'</table></div>';
        }
        else{
            echo '<p>nothing found</p>';
        }
?>
</div>

<div class="row">
<div class="col-sm-3 col-sm-offset-9">
    <span class="help-block"><?php echo $rad_err; ?></span>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Buy">
    </div>  
</div>
</div>

</body>
</html>