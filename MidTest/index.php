<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MidTest - Index</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    if (isset($_GET['delete'])) {
        $std_id = $_GET['delete'];
        $stmt = $conn->prepare("DELETE FROM tb_664230033 WHERE `key` = ?");
        $stmt->execute([$std_id]);
        $_SESSION['success'] = "ลบข้อมูลเรียบร้อยแล้ว";
        header("Location: index.php");
        exit;
    }

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
                    <th>Age</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Created At</th>
                    <th>Manage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $index => $student): ?>
                    <tr>
                        <td class="align-middle">
                            <span><?= $index + 1 ?></span>
                        </td>
                        <td class="align-middle">
                            <span><?= htmlspecialchars($student['std_id']) ?></span>
                        </td>
                        <td class="align-middle">
                            <span><?= htmlspecialchars($student['f_name']) ?></span>
                        </td>
                        <td class="align-middle">
                            <span><?= htmlspecialchars($student['l_name']) ?></span>
                        </td>
                        <td class="align-middle">
                            <span><?= htmlspecialchars($student['age']) ?></span>
                        </td>
                        <td class="align-middle">
                            <span><?= htmlspecialchars($student['mail']) ?></span>
                        </td>
                        <td class="align-middle">
                            <span><?= htmlspecialchars($student['tel']) ?></span>
                        </td>
                        <td class="align-middle">
                            <span><?= htmlspecialchars($student['created_at']) ?></span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="edit.php?id=<?= $student['key'] ?>"
                                    class="btn btn-warning btn-sm d-flex justify-content-center align-items-center">
                                    <i class="fas fa-edit me-1"></i>แก้ไข
                                </a>
                                <button type="button" class="btn btn-danger btn-sm d-flex justify-content-center align-items-center delete-btn"
                                    data-id="<?= $student['key'] ?>"
                                    data-name="<?= htmlspecialchars($student['f_name']) ?>">
                                    <i class="fas fa-trash me-1"></i>ลบ
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        let table = new DataTable('#productTable');

        // SweetAlert for Delete Product
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const stdId = this.getAttribute('data-id');
                const stdName = this.getAttribute('data-name');

                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    html: `คุณต้องการลบข้อมูลของ <strong>${stdName}</strong> หรือไม่?<br><small class="text-danger">การลบจะไม่สามารถกู้คืนได้!</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'กำลังลบสินค้า...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        window.location.href = `index.php?delete=${stdId}`;
                    }
                });
            });
        });
        // SweetAlert for Add/Update Success
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '<?= $_SESSION['success'] ?>',
                showConfirmButton: false,
                timer: 2000
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </script>
</body>

</html>