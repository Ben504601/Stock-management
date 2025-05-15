<?php
// StockManagement.php — no sessions, message via GET

// 1) Connect
$mysqli = new mysqli('localhost','root','','store_information_system');
if($mysqli->connect_errno) {
    die("MySQL error: ".$mysqli->connect_error);
}

// 2) Initialize a local $msg
$msg = '';

// 3) DELETE handler
if(isset($_GET['delete'], $_GET['confirm']) && $_GET['confirm']==='yes') {
    $vid = (int)$_GET['delete'];

    $stmt = $mysqli->prepare("DELETE FROM stock WHERE variationID=?");
    $stmt->bind_param('i',$vid);
    $stmt->execute(); $stmt->close();

    $stmt = $mysqli->prepare("DELETE FROM variation WHERE variationID=?");
    $stmt->bind_param('i',$vid);
    $stmt->execute(); $stmt->close();

    $msg = "Deleted variation #{$vid}.";
    header("Location: StockManagement.php?msg=".urlencode($msg));
    exit;
}

// 4) EDIT handler
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['edit_product'])) {
    $vid   = (int)$_POST['variationID'];
    $name  = trim($_POST['name']);
    $desc  = trim($_POST['description']);
    $size  = trim($_POST['size']);
    $price = (float)$_POST['price'];
    $qty   = (int)$_POST['quantity_on_hand'];
    $reord = (int)$_POST['reorder_level'];

    // update inventory+variation
    $stmt = $mysqli->prepare("
      UPDATE inventory i
      JOIN variation v ON v.productID = i.productID
      SET i.name=?, i.description=?, v.weight=?, v.price=?
      WHERE v.variationID=?
    ");
    $stmt->bind_param('sssdi',$name,$desc,$size,$price,$vid);
    $ok1 = $stmt->execute(); $stmt->close();

    // update stock
    $stmt = $mysqli->prepare("
      UPDATE stock
      SET quantity_on_hand=?, reorder_level=?
      WHERE variationID=?
    ");
    $stmt->bind_param('iii',$qty,$reord,$vid);
    $ok2 = $stmt->execute(); $stmt->close();

    $msg = $ok1 && $ok2
         ? "Updated variation #{$vid}."
         : "Error updating variation #{$vid}.";
    header("Location: StockManagement.php?msg=".urlencode($msg));
    exit;
}

// 5) ADD handler
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_product'])) {
    $name  = trim($_POST['name']);
    $desc  = trim($_POST['description']);
    $size  = trim($_POST['size']);
    $price = (float)$_POST['price'];
    $qty   = (int)$_POST['quantity_on_hand'];
    $reord = (int)$_POST['reorder_level'];

    // inventory
    $stmt = $mysqli->prepare("INSERT INTO inventory(name,description) VALUES(?,?)");
    $stmt->bind_param('ss',$name,$desc);
    $ok1 = $stmt->execute();
    $pid = $stmt->insert_id;
    $stmt->close();

    // variation
    $stmt = $mysqli->prepare("INSERT INTO variation(productID,weight,price) VALUES(?,?,?)");
    $stmt->bind_param('isd',$pid,$size,$price);
    $ok2 = $stmt->execute();
    $vid = $stmt->insert_id;
    $stmt->close();

    // stock
    $stmt = $mysqli->prepare("INSERT INTO stock(productID,variationID,quantity_on_hand,reorder_level) VALUES(?,?,?,?)");
    $stmt->bind_param('iiii',$pid,$vid,$qty,$reord);
    $ok3 = $stmt->execute();
    $stmt->close();

    $msg = ($ok1 && $ok2 && $ok3)
         ? "Added new variation #{$vid}."
         : "Error adding new product.";
    header("Location: StockManagement.php?msg=".urlencode($msg));
    exit;
}

// 6) Fetch all rows
$sql = "SELECT i.name AS product_name,
               i.description,
               v.variationID,
               v.weight AS size,
               v.price,
               s.quantity_on_hand,
               s.reorder_level
        FROM variation v
        JOIN inventory i ON v.productID = i.productID
        JOIN stock s     ON s.variationID = v.variationID
        ORDER BY i.name, v.weight";
$result = $mysqli->query($sql);
$rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// 7) Counters
$total = count($rows);
$low   = 0; $out = 0;
foreach($rows as $r) {
    if($r['quantity_on_hand']==0) $out++;
    elseif($r['quantity_on_hand'] <= $r['reorder_level']) $low++;
}

// 8) Grab any ?msg= from redirect
if(isset($_GET['msg'])) {
    $msg = htmlspecialchars($_GET['msg']);
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

        <?php if($msg): ?>
            <div class="message"><?= $msg ?></div>
        <?php endif; ?>

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
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="message"><?= $_SESSION['message'] ?></div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
            </header>

            <section class="stock-management">
                <h1>Stock Management</h1>
                <p>Monitor and update inventory levels</p>

                <div class="boxzone">
                    <div class="box" id="totalItemsBox">
                        <p>Total Items</p>
                        <h1 class="number"><?= $total ?></h1>
                    </div>
                    <div class="box" id="lowStockBox">
                        <p>Low Stock Items</p>
                        <h1 class="number" id="warning"><?= $low ?></h1>
                    </div>
                    <div class="box" id="outStockBox">
                        <p>Out Of Stock Items</p>
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
                        <?php foreach ($rows as $r):
                            $status = $r['quantity_on_hand'] == 0 ? 'No Stock' : ($r['quantity_on_hand'] <= $r['reorder_level'] ? 'Low Stock' : 'In Stock');
                            $cls = $r['quantity_on_hand'] == 0 ? 'red' : ($r['quantity_on_hand'] <= $r['reorder_level'] ? 'yellow' : 'teal');
                            $r = array_map(fn($v)=>$v??'', $r);
                            $json = json_encode($r, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($r['product_name']) ?></td>
                            <td><?= htmlspecialchars($r['size']) ?></td>
                            <td><?= $r['quantity_on_hand'] ?></td>
                            <td><?= $r['reorder_level'] ?></td>
                            <td>₱<?= number_format($r['price'], 2) ?></td>
                            <td><span class="badge <?= $cls ?>"><?= $status ?></span></td>
                            <td>
                                <a href="#" class="edit" data-product="<?= htmlspecialchars($json, ENT_QUOTES) ?>">Edit</a>
                                <a href="#" class="delete" data-variation-id="<?= $r['variationID'] ?>">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- Add Product Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span id="addModalClose" class="modal-close">×</span>
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
                <input type="number" name="price" step="0.01" placeholder="Price e.g., 150.00" min="0" required>
                <input type="number" name="quantity_on_hand" placeholder="Qty" min="0" required>
                <input type="number" name="reorder_level" placeholder="Reorder Level" min="0" required>
                <button type="submit" name="add_product">Save Product</button>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span id="deleteModalClose" class="modal-close">×</span>
            <h2>Confirm Delete</h2>
            <p>Are you sure you want to delete this product?</p>
            <button id="confirmDeleteYes">Yes</button>
            <button id="confirmDeleteNo">No</button>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span id="editModalClose" class="modal-close">×</span>
            <h2>Edit Product</h2>
            <form method="post">
                <input type="hidden" name="variationID" id="editVariationID">
                <input name="name" id="editName" placeholder="Product Name" required>
                <input name="description" id="editDescription" placeholder="Description">
                <select name="size" id="editSize" required>
                    <option value="" disabled>Select a size</option>
                    <option value="5kg">5kg</option>
                    <option value="10kg">10kg</option>
                    <option value="25kg">25kg</option>
                    <option value="50kg">50kg</option>
                </select>
                <input type="number" name="price" id="editPrice" step="0.01" placeholder="Price e.g., 150.00" min="0" required>
                <input type="number" name="quantity_on_hand" id="editQty" placeholder="Qty" min="0" required>
                <input type="number" name="reorder_level" id="editReorder" placeholder="Reorder Level" min="0" required>
                <button type="submit" name="edit_product">Save Changes</button>
            </form>
        </div>
    </div>

    <script src="StockManagement.js" defer></script>
</body>
</html>