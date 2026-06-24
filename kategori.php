<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

$errorMsg = "";
$successMsg = "";

// Handle Delete Category
if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // Check if category is being used by any issues
    $checkQuery = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM isu WHERE id_kategori = '$id_hapus'");
    $checkData = mysqli_fetch_assoc($checkQuery);
    
    if ($checkData['total'] > 0) {
        $errorMsg = "Gagal menghapus! Kategori ini sedang digunakan oleh {$checkData['total']} isu. Hapus isu terlebih dahulu.";
    } else {
        $deleteQuery = mysqli_query($koneksi, "DELETE FROM kategori_isu WHERE id_kategori = '$id_hapus'");
        if ($deleteQuery) {
            $successMsg = "Kategori berhasil dihapus!";
        } else {
            $errorMsg = "Gagal menghapus kategori: " . mysqli_error($koneksi);
        }
    }
}

// Handle Add Category
if (isset($_POST['tambah'])) {
    $nama_kategori = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    if (!empty(trim($nama_kategori))) {
        $insertQuery = mysqli_query($koneksi, "INSERT INTO kategori_isu (nama_kategori) VALUES ('$nama_kategori')");
        if ($insertQuery) {
            $successMsg = "Kategori '{$nama_kategori}' berhasil ditambahkan!";
        } else {
            $errorMsg = "Gagal menambahkan kategori: " . mysqli_error($koneksi);
        }
    } else {
        $errorMsg = "Nama kategori tidak boleh kosong.";
    }
}

// Handle Edit Category (Form Submission)
if (isset($_POST['update'])) {
    $id_update = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $nama_kategori = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    if (!empty(trim($nama_kategori))) {
        $updateQuery = mysqli_query($koneksi, "UPDATE kategori_isu SET nama_kategori = '$nama_kategori' WHERE id_kategori = '$id_update'");
        if ($updateQuery) {
            $successMsg = "Kategori berhasil diperbarui!";
            // Redirect to clear edit state
            header("Refresh: 1; url=kategori.php");
        } else {
            $errorMsg = "Gagal memperbarui kategori: " . mysqli_error($koneksi);
        }
    } else {
        $errorMsg = "Nama kategori tidak boleh kosong.";
    }
}

// Fetch Category for Editing (if active)
$editData = null;
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $editQuery = mysqli_query($koneksi, "SELECT * FROM kategori_isu WHERE id_kategori = '$id_edit'");
    $editData = mysqli_fetch_assoc($editQuery);
}

// Fetch all categories with issues count
$query = mysqli_query($koneksi, "
    SELECT kategori_isu.*, COUNT(isu.id_isu) AS total_isu 
    FROM kategori_isu 
    LEFT JOIN isu ON kategori_isu.id_kategori = isu.id_kategori 
    GROUP BY kategori_isu.id_kategori 
    ORDER BY kategori_isu.id_kategori ASC
");

$pageTitle = "Manajemen Kategori";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="row">
    <div class="col-12">
        <h4 class="text-navy fw-bold mb-4"><i class="fa-regular fa-bookmark me-2 text-primary"></i>Manajemen Kategori Isu</h4>
    </div>
</div>

<!-- Notification Messages -->
<?php if (!empty($errorMsg)) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>
        <div><?= $errorMsg; ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($successMsg)) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>
        <div><?= $successMsg; ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left Column: Categories List -->
    <div class="col-12 col-lg-8">
        <div class="card-custom h-100">
            <div class="card-header-custom">
                <h5>Daftar Kategori</h5>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive table-responsive-custom">
                    <table class="table table-custom table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 50%;">Nama Kategori</th>
                                <th style="width: 20%;" class="text-center">Total Isu</th>
                                <th style="width: 20%;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($query) > 0) {
                                while ($data = mysqli_fetch_assoc($query)) {
                            ?>
                                    <tr class="fade-in-up">
                                        <td>#<?= $data['id_kategori']; ?></td>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($data['nama_kategori']); ?></td>
                                        <td class="text-center">
                                            <span class="text-plain-gray fw-semibold">
                                                <?= $data['total_isu']; ?> Isu
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="kategori.php?edit=<?= $data['id_kategori']; ?>" class="action-icon-btn action-icon-edit" title="Edit">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </a>
                                                <a href="kategori.php?hapus=<?= $data['id_kategori']; ?>" class="action-icon-btn action-icon-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')" title="Hapus">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                            <?php 
                                } 
                            } else {
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Belum ada data kategori.
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Add/Edit Category Form -->
    <div class="col-12 col-lg-4">
        <div class="card-custom sticky-top" style="top: 20px;">
            <div class="card-header-custom bg-white border-bottom">
                <h5>
                    <?php if ($editData) : ?>
                        <i class="fa-regular fa-pen-to-square me-2 text-warning"></i>Edit Kategori
                    <?php else : ?>
                        <i class="fa-regular fa-square-plus me-2 text-primary"></i>Tambah Kategori
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body-custom">
                <form method="POST">
                    <?php if ($editData) : ?>
                        <input type="hidden" name="id_kategori" value="<?= $editData['id_kategori']; ?>">
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small">Nama Kategori</label>
                        <input type="text" 
                               name="nama_kategori" 
                               class="form-control" 
                               placeholder="Contoh: Sarana Prasarana" 
                               value="<?= $editData ? htmlspecialchars($editData['nama_kategori']) : ''; ?>"
                               required>
                    </div>

                    <div class="d-flex gap-2">
                        <?php if ($editData) : ?>
                            <a href="kategori.php" class="btn btn-secondary-custom flex-grow-1">Batal</a>
                            <button type="submit" name="update" class="btn btn-warning-custom text-white flex-grow-1">Update</button>
                        <?php else : ?>
                            <button type="submit" name="tambah" class="btn btn-primary-custom w-100"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
