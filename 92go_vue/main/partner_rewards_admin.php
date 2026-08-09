<?php
session_start();
if($_SESSION['unohs'] == null){
    header("location:index.php?msg=unauthorized");
}
include ("conn.php");

// Create table if it doesn't exist
$createTable = "CREATE TABLE IF NOT EXISTS partner_reward_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recharge_amount DECIMAL(10,2) NOT NULL,
    bet_amount DECIMAL(10,2) NOT NULL,
    reward_amount DECIMAL(10,2) NOT NULL,
    type INT NOT NULL,
    days INT NOT NULL DEFAULT 7,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $createTable);

// Handle new reward setting
if(isset($_POST['add_reward'])) {
    $recharge = mysqli_real_escape_string($conn, $_POST['recharge_amount']);
    $bet = mysqli_real_escape_string($conn, $_POST['bet_amount']);
    $reward = mysqli_real_escape_string($conn, $_POST['reward_amount']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    
    $sql = "INSERT INTO partner_reward_settings (recharge_amount, bet_amount, reward_amount, type) 
            VALUES ('$recharge', '$bet', '$reward', '$type')";
    if(mysqli_query($conn, $sql)) {
        echo '<script>alert("Reward setting added successfully");</script>';
    } else {
        echo '<script>alert("Failed to add reward setting");</script>';
    }
}

// Handle update
if(isset($_POST['update_reward'])) {
    $id = mysqli_real_escape_string($conn, $_POST['reward_id']);
    $recharge = mysqli_real_escape_string($conn, $_POST['recharge_amount']);
    $bet = mysqli_real_escape_string($conn, $_POST['bet_amount']);
    $reward = mysqli_real_escape_string($conn, $_POST['reward_amount']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    
    $sql = "UPDATE partner_reward_settings 
            SET recharge_amount='$recharge', bet_amount='$bet', reward_amount='$reward', type='$type' 
            WHERE id='$id'";
    if(mysqli_query($conn, $sql)) {
        echo '<script>alert("Reward setting updated successfully");</script>';
    } else {
        echo '<script>alert("Failed to update reward setting");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Partner Rewards Admin</title>
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/feather/feather.css">
    <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css"/>
    <link rel="stylesheet" href="vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="vendors/jquery-bar-rating/fontawesome-stars-o.css">
    <link rel="stylesheet" href="vendors/jquery-bar-rating/fontawesome-stars.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="images/favicon.png" />
    <style>
        .cool-input {
            border: 2px solid #007bff;
            border-radius: 0.25rem;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .cool-input:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .cool-input::placeholder {
            color: #6c757d;
            opacity: 1;
        }
        .cool-button {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
        }
        .cool-button:hover {
            background-color: #0056b3;
            color: #fff;
        }
        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo" href="dashboard.php"><img src="images/logo.png" alt="logo"/></a>
                <a class="navbar-brand brand-logo-mini" href="dashboard.php"><img src="images/logo-mini.png" alt="logo"/></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="icon-menu"></span>
                </button>       
                <ul class="navbar-nav navbar-nav-right">           
                    <li class="nav-item dropdown d-flex mr-4 ">
                        <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center" id="notificationDropdown" href="#" data-toggle="dropdown">
                            <i class="icon-cog"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                            <p class="mb-0 font-weight-normal float-left dropdown-header">Settings</p>              
                            <a class="dropdown-item preview-item" href="logout.php">
                                <i class="icon-inbox"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                    <span class="icon-menu"></span>
                </button>
            </div>
        </nav>
        <div class="container-fluid page-body-wrapper">
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <div class="user-profile">
                    <div class="user-image">
                        <img src="images/faces/face28.png">
                    </div>
                    <div class="user-name">ALADDINN GAME</div>
                    <div class="user-designation">Admin</div>
                </div>
                <?php include 'compass.php';?>
            </nav>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-sm-12 mb-4 mb-xl-0">
                            <h4 class="font-weight-bold text-dark">Partner Rewards Management</h4>
                        </div>
                    </div>
                    
                    <!-- Add New Reward Setting -->
                    <div class="row">
                        <form action="#" method="post" class="col-md-6">
                            <h5>Add New Reward Setting</h5>
                            <div class="form-group">
                                <input name="recharge_amount" type="number" step="0.01" placeholder="Recharge Amount" class="form-control cool-input" required />
                            </div>
                            <div class="form-group">
                                <input name="bet_amount" type="number" step="0.01" placeholder="Required Bet Amount" class="form-control cool-input" required />
                            </div>
                            <div class="form-group">
                                <input name="reward_amount" type="number" step="0.01" placeholder="Reward Amount" class="form-control cool-input" required />
                            </div>
                            <div class="form-group">
                                <select name="type" class="form-control cool-input" required>
                                    <option value="1">1st Deposit</option>
                                    <option value="2">2nd Deposit</option>
                                    <option value="3">3rd Deposit</option>
									<option value="4">4th Deposit</option>
									<option value="5">5th Deposit</option>
									<option value="6">6th Deposit</option>
									<option value="7">7th Deposit</option>
									<option value="8">8th Deposit</option>
                                </select>
                            </div>
                            <button type="submit" name="add_reward" class="btn btn-primary cool-button">Add Reward</button>
                        </form>
                    </div>

                    <!-- Existing Reward Settings -->
                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <h5>Existing Reward Settings</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Recharge Amount</th>
                                        <th>Bet Amount</th>
                                        <th>Reward Amount</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($conn, "SELECT * FROM partner_reward_settings");
                                    while($row = mysqli_fetch_array($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['recharge_amount']; ?></td>
                                        <td><?php echo $row['bet_amount']; ?></td>
                                        <td><?php echo $row['reward_amount']; ?></td>
                                        <td><?php echo $row['type'] == 1 ? '1st Deposit' : ($row['type'] == 2 ? '2nd Deposit' : ($row['type'] == 3 ? '3rd Deposit' : ($row['type'] == 4 ? '4th Deposit' : ($row['type'] == 5 ? '5th Deposit' : ($row['type'] == 6 ? '6th Deposit' : ($row['type'] == 7 ? '7th Deposit' : ($row['type'] == 8 ? '8th Deposit' : ''))))))); ?></td>
                                        <td>
                                            <form action="#" method="post" class="d-inline">
                                                <input type="hidden" name="reward_id" value="<?php echo $row['id']; ?>">
                                                <input name="recharge_amount" type="number" step="0.01" value="<?php echo $row['recharge_amount']; ?>" class="form-control cool-input d-inline" style="width: 100px;" required />
                                                <input name="bet_amount" type="number" step="0.01" value="<?php echo $row['bet_amount']; ?>" class="form-control cool-input d-inline" style="width: 100px;" required />
                                                <input name="reward_amount" type="number" step="0.01" value="<?php echo $row['reward_amount']; ?>" class="form-control cool-input d-inline" style="width: 100px;" required />
                                                <select name="type" class="form-control cool-input d-inline" style="width: 120px;" required>
                                                    <option value="1" <?php if($row['type'] == 1) echo 'selected'; ?>>1st Deposit</option>
    <option value="2" <?php if($row['type'] == 2) echo 'selected'; ?>>2nd Deposit</option>
    <option value="3" <?php if($row['type'] == 3) echo 'selected'; ?>>3rd Deposit</option>
    <option value="4" <?php if($row['type'] == 4) echo 'selected'; ?>>4th Deposit</option>
    <option value="5" <?php if($row['type'] == 5) echo 'selected'; ?>>5th Deposit</option>
    <option value="6" <?php if($row['type'] == 6) echo 'selected'; ?>>6th Deposit</option>
    <option value="7" <?php if($row['type'] == 7) echo 'selected'; ?>>7th Deposit</option>
    <option value="8" <?php if($row['type'] == 8) echo 'selected'; ?>>8th Deposit</option>
                                                </select>
                                                <button type="submit" name="update_reward" class="btn btn-success cool-button">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © 98lottery.online 2025</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>
    <script src="vendors/base/vendor.bundle.base.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>