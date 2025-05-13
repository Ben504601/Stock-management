<?php
// StockManagement.php

//–– 1) Database connection (adjust your creds)
$mysqli = new mysqli('localhost','root','','store_information_system');
if($mysqli->connect_errno) {
  die("MySQL error: ".$mysqli->connect_error);
}

// Handle delete
if(isset($_GET['delete'])) {
    $vid = (int)$_GET['delete'];

    // delete stock then variation
    $mysqli->query("DELETE FROM stock WHERE variationID = $vid");
    $mysqli->query("DELETE FROM variation WHERE variationID = $vid");
    header("Location: StockManagement.php");
    exit;
}

// Handle add
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_product'])) {
    $name         = $_POST['name'];
    $description  = $_POST['description'];
    $size         = $_POST['size'];
    $price        = (float)$_POST['price'];
    $qty          = (int)$_POST['quantity_on_hand'];
    $reorder      = (int)$_POST['reorder_level'];

    // inventory
    $stmt = $mysqli->prepare("INSERT INTO inventory(name, description) VALUES(?,?)");
    $stmt->bind_param('ss',$name,$description);
    $stmt->execute();
    $pid = $stmt->insert_id;
    $stmt->close();

    // variation
    $stmt = $mysqli->prepare("INSERT INTO variation(productID, weight, price) VALUES(?,?,?)");
    $stmt->bind_param('isd', $pid, $size, $price);
    $stmt->execute();
    $vid = $stmt->insert_id;
    $stmt->close();

    // stock
    $stmt = $mysqli->prepare("INSERT INTO stock(productID, variationID, quantity_on_hand, reorder_level) VALUES(?,?,?,?)");
    $stmt->bind_param('iiii', $pid, $vid, $qty, $reorder);
    $stmt->execute();
    $stmt->close();

    header("Location: StockManagement.php");
    exit;
}

// Fetch data
$sql = "SELECT
            i.name as product_name,
            v.variationID,
            v.weight as size,
            v.price,
            s.quantity_on_hand,
            s.reorder_level
        FROM variation v
        JOIN inventory i ON v.productID = i.productID
        JOIN stock s ON v.variationID = s.variationID
        ORDER BY i.name, v.weight";
$rows = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);

// Counters
$total = count($rows);
$low = 0;
$out = 0;
foreach($rows as $r) {
    if($r['quantity_on_hand']==0) $out++;
    elseif($r['quantity_on_hand'] <= $r['reorder_level']) $low++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management - Bugasan ni Mayang</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="StockManagement.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Bugasan ni<br>Mayang</h2>
            <nav>
                <p class="extension">MAIN</p>
                <ul>
                    <li><a href="Dashboard.html">Dashboard</a></li>
                    <li><a href="UserManagement.html">User Management</a></li>
                    <li class="active">Products</li>
                    <li><a href="">Suppliers</a></li>
                    <li><a href="Report.html">Reports</a></li>
                </ul>
            </nav>
            <div class="settings">
                <p class="extension">SETTINGS</p>
                <ul>
                    <li><a href="">Settings</a></li>
                    <li><a href="">Logout</a></li>
                </ul>
            </div>
        </aside>

        <main class="main-content">
            <header>
                <div class="userprofile" onclick="toggledropdown()">
                    <div class="avatar">A</div>
                    <span class="username"> Admin User </span>
                    <span class="arrow">▼</span>
                    <div id="dropdown" class="dropdownmenu">
                        <a href="#">Profile</a>
                        <a href="#">About</a>
                    </div>
                </div>
            </header>

            <section class="stock-management">
                <h1>Stock Management</h1>
                <p>Monitor and update inventory levels</p>

                <div class="boxzone">
                    <div class="box" id="totalItemsBox">
                        <p> Total Items</p>
                        <h1 class="number"><?= $total ?></h1>
                    </div>
        
                    <div class="box" id="lowStockBox">
                        <p> Low Stock Items </p>
                        <h1 class="number" id="warning"><?= $low ?></h1>
                    </div>
        
                    <div class="box" id="outStockBox">
                        <p> Out Of Stock Items </p>
                        <h1 class="number" id="attention"><?= $out ?></h1>
                    </div>
                </div>

                <div class="search-filter">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input id="searchInput" class="searchbar" type="text" placeholder="Search products...">
                        
                        <select id="sizeFilter">
                            <option value="all">All Sizes</option>
                            <option value="50kg">50kg</option>
                            <option value="25kg">25kg</option>
                            <option value="10kg">10kg</option>
                            <option value="5kg">5kg</option>
                          </select>

                          <select id="statusFilter">
                            <option value="all">All Status</option>
                            <option value="In Stock">In Stock</option>
                            <option value="Low Stock">Low Stock</option>
                            <option value="No Stock">No Stock</option>
                        </select>

                        <button id="addBtn">+ Add Product</button>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Size</th>
                            <th>Current Stock</th>
                            <th>Reorder Level</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rows as $r):
                            // determine status badge
                            if($r['quantity_on_hand']==0) {
                                $status = 'No Stock'; $cls='red';
                            } elseif($r['quantity_on_hand'] <= $r['reorder_level']) {
                                $status = 'Low Stock'; $cls='yellow';
                            } else {
                                $status = 'In Stock'; $cls='teal';
                            }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($r['product_name']) ?></td>
                            <td><?= htmlspecialchars($r['size']) ?></td>
                            <td><?= $r['quantity_on_hand'] ?></td>
                            <td><?= $r['reorder_level'] ?></td>
                            <td>₱<?= number_format($r['price'],2) ?></td>
                            <td><span class="badge <?= $cls ?>"><?= $status ?></span></td>
                            <td>
                                <a href="edit.php?vid=<?= $r['variationID'] ?>" class="edit">Edit</a>
                                <a href="delete.php?delete=<?= $r['variationID'] ?>" class="delete">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <span id="modalClose" class="modal-close">&times;</span>
            <h2>Add New Product</h2>
            <form method="post">
                <input name="name" placeholder="Product Name" required>
                <input name="description" placeholder="Description">
                <select name="size" required>
                    <option value="" disabled selected>Select a size</option>
                    <option value="5kg">5kg</option>
                    <option value="10kg">10kg</option>
                    <option value="25kg">25kg</option>
                    <option value="50kg">50kg</option>
                </select>
                <input type="number" name="price" step="0.01" placeholder="Price e.g., 150.00" required>
                <input type="number" name="quantity_on_hand" placeholder="Qty" required>
                <input type="number" name="reorder_level" placeholder="Reorder Level" required>
                <button type="submit" name="add_product">Save Product</button>
            </form>
        </div>
    </div>

    <script src="StockManagement.js"></script>
</body>
</html>