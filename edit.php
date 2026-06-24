<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

$query = mysqli_query($koneksi, "SELECT * FROM isu WHERE id_isu='$id'");
$d = mysqli_fetch_assoc($query);

if (!$d) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['update'])) {
    $nama_pelapor = mysqli_real_escape_string($koneksi, $_POST['nama_pelapor']);
    $id_kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $judul_isu = mysqli_real_escape_string($koneksi, $_POST['judul_isu']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status_isu']);
    $prioritas = mysqli_real_escape_string($koneksi, $_POST['prioritas']);

    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    $foto_name = $d['foto']; // Keep existing photo as default

    if (!empty($foto)) {
        // Generate unique name for the new file
        $ext = pathinfo($foto, PATHINFO_EXTENSION);
        $foto_name = time() . '_' . uniqid() . '.' . $ext;
        
        // Ensure uploads directory exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        // Move new file
        if (move_uploaded_file($tmp, "uploads/" . $foto_name)) {
            // Delete old file if exists
            $oldFotoPath = "uploads/" . $d['foto'];
            if (!empty($d['foto']) && file_exists($oldFotoPath)) {
                unlink($oldFotoPath);
            }
        }
    }

    $updateQuery = mysqli_query($koneksi, "
        UPDATE isu SET
            nama_pelapor = '$nama_pelapor',
            id_kategori = '$id_kategori',
            judul_isu = '$judul_isu',
            deskripsi = '$deskripsi',
            status_isu = '$status',
            prioritas = '$prioritas',
            foto = '$foto_name'
        WHERE id_isu = '$id'
    ");

    if (!$updateQuery) {
        die(mysqli_error($koneksi));
    }

    header("Location: index.php");
    exit();
}

$pageTitle = "Edit Isu";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-7">
        <h4 class="text-navy fw-bold mb-4">
            <a href="index.php" class="text-decoration-none text-muted fw-normal"><i class="fa-solid fa-arrow-left me-2"></i>Dashboard</a> 
            <span class="text-muted mx-2">/</span> Edit Laporan
        </h4>

        <div class="card-custom">
            <div class="card-header-custom">
                <h5><i class="fa-regular fa-pen-to-square me-2 text-primary"></i>Edit Data Isu #<?= $d['id_isu']; ?></h5>
            </div>
            
            <div class="card-body-custom">
                <form method="POST" enctype="multipart/form-data">
                    <!-- Nama Pelapor -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pelapor</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-regular fa-user"></i></span>
                            <input type="text" name="nama_pelapor" value="<?= htmlspecialchars($d['nama_pelapor']); ?>" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Kategori Isu -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">Kategori Isu</label>
                            <select name="id_kategori" class="form-select" required>
                                <?php
                                $kategori = mysqli_query($koneksi, "SELECT * FROM kategori_isu");
                                while ($k = mysqli_fetch_assoc($kategori)) {
                                    $selected = ($d['id_kategori'] == $k['id_kategori']) ? 'selected' : '';
                                    echo "<option value='{$k['id_kategori']}' {$selected}>{$k['nama_kategori']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <!-- Prioritas -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">Prioritas</label>
                            <select name="prioritas" class="form-select">
                                <option value="Rendah" <?= $d['prioritas'] == 'Rendah' ? 'selected' : ''; ?>>Rendah</option>
                                <option value="Sedang" <?= ($d['prioritas'] == 'Sedang' || empty($d['prioritas'])) ? 'selected' : ''; ?>>Sedang</option>
                                <option value="Tinggi" <?= $d['prioritas'] == 'Tinggi' ? 'selected' : ''; ?>>Tinggi</option>
                            </select>
                        </div>
                    </div>

                    <!-- Judul Isu -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Isu</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-regular fa-lightbulb"></i></span>
                            <input type="text" name="judul_isu" value="<?= htmlspecialchars($d['judul_isu']); ?>" class="form-control" required>
                        </div>
                    </div>

                    <!-- Deskripsi Isu -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Isu</label>
                        <textarea name="deskripsi" class="form-control" rows="5" required><?= htmlspecialchars($d['deskripsi']); ?></textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Status -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">Status Laporan</label>
                            <select name="status_isu" class="form-select">
                                <option value="Menunggu" <?= ($d['status_isu'] == 'Menunggu' || empty($d['status_isu'])) ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="Diproses" <?= $d['status_isu'] == 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
                                <option value="Selesai" <?= $d['status_isu'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                        </div>
                        
                        <!-- File Upload -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">Ganti Foto Bukti / Lampiran</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                            <div class="form-text extra-small text-muted">Biarkan kosong jika tidak ingin mengganti foto.</div>
                        </div>
                    </div>

                    <!-- Current Attachment preview -->
                    <?php if (!empty($d['foto']) && file_exists('uploads/' . $d['foto'])) : ?>
                        <div class="mb-4 p-3 border rounded bg-light">
                            <label class="form-label fw-semibold d-block">Foto Lampiran Saat Ini:</label>
                            <div class="d-flex align-items-center gap-3">
                                <img src="uploads/<?= $d['foto']; ?>" class="img-thumbnail" style="max-height: 100px; max-width: 150px; object-fit: cover;">
                                <div class="small text-muted">
                                    <i class="fa-solid fa-file-image me-1"></i> <?= htmlspecialchars($d['foto']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 justify-content-end border-top pt-3">
                        <a href="index.php" class="btn btn-secondary-custom">
                            Batal
                        </a>
                        <button type="submit" name="update" class="btn btn-warning-custom px-4 text-white" style="border-radius: 4px;">
                            <i class="fa-regular fa-circle-check me-2"></i>Update Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>