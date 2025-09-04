<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MidTest - Index</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet">
    <style>
        * {
            font-family: sans-serif;
        }

        body {
            margin: 20px;
        }

        p {
            color: #333;
            font-size: 16px;
        }

        .container {
            max-width: 1200px;
        }
    </style>
</head>

<body>
    <?php
    session_start();

    require 'connect.php';

    $sql = "SELECT * FROM tb_664230033";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // print_r($data);

    ?>
    <div class="container mt-5">
        <?php if (isset($_GET['insert']) && $_GET['insert'] === 'success'): ?>
            <div class="alert alert-success">เพิ่มข้อมูลสำเร็จ</div>
        <?php endif; ?>
        <div class="d-flex justify-content-between">
            <h2>รายการนักศึกษา</h2>
            <a href="insert.php" class="btn btn-success d-inline justify-content-center align-content-center">+ เพิ่มนักศึกษา</a>
        </div>
        <form action="" method="post" class="mb-3">
        </form>
        <table class="table table-striped table-bordered" id="productTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // if(isset($_POST['price']) && !empty($_POST['price'])) {
                //     $filterPrice = $_POST['price'];
                //     $filteredProducts = array_filter($products, function($product) use ($filterPrice){
                //         return $product['price'] == $filterPrice;
                //     });
                // $filteredProducts = array_values($filteredProducts);
                // } else {
                $filteredProducts = $data;
                // }


                foreach ($filteredProducts as $index => $product) {
                    echo "<tr>";
                    echo "<td>" . ($index + 1) . "</td>";
                    echo "<td>" . $product['student_id'] . "</td>";
                    echo "<td>" . $product['first_name'] . "</td>";
                    echo "<td>" . $product['last_name'] . "</td>";
                    echo "<td>" . $product['email'] . "</td>";
                    echo "<td>" . $product['phone'] . "</td>";
                    echo "<td>" . $product['timestamp'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        let table = new DataTable('#productTable');
    </script>
</body>

</html>