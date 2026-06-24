<?php
session_start();
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $nama_pelapor = mysqli_real_escape_string($koneksi, $_POST['nama_pelapor']);
    $id_kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $judul_isu = mysqli_real_escape_string($koneksi, $_POST['judul_isu']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $status = 'Menunggu'; // Hardcode status to 'Menunggu' for safety
    $prioritas = mysqli_real_escape_string($koneksi, $_POST['prioritas']);

    $foto = $_FILES['foto']['name']; 
    $tmp = $_FILES['foto']['tmp_name'];
    $foto_name = "";

    if (!empty($foto)) {
        // Generate unique name for the uploaded file to avoid duplicate name collision
        $ext = pathinfo($foto, PATHINFO_EXTENSION);
        $foto_name = time() . '_' . uniqid() . '.' . $ext;
        
        // Ensure uploads directory exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        move_uploaded_file($tmp, "uploads/" . $foto_name);
    }

    $query = mysqli_query($koneksi, "
        INSERT INTO isu 
        (
            nama_pelapor, 
            id_kategori, 
            judul_isu, 
            deskripsi, 
            status_isu, 
            prioritas, 
            foto, 
            tanggal_lapor
        ) 
        VALUES 
        (
            '$nama_pelapor', 
            '$id_kategori', 
            '$judul_isu', 
            '$deskripsi', 
            '$status', 
            '$prioritas', 
            '$foto_name', 
            CURDATE()
        )
    ");

    if (!$query) {
        die(mysqli_error($koneksi));
    }

    header("Location: index.php?success=1");
    exit();
}

$pageTitle = "Kirim Isu Laporan";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-7">
        <h4 class="text-navy fw-bold mb-4">
            <i class="fa-regular fa-paper-plane me-2 text-primary"></i>Kirim Laporan Isu Kampus
        </h4>

        <?php if (isset($_GET['success'])) : ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="border-radius: 4px; background-color: #e6f4ea; color: #2e5a3c; border-color: #a7f3d0;">
                <i class="fa-solid fa-circle-check me-2"></i>
                <div>
                    <strong>Laporan Berhasil Terkirim!</strong> Terima kasih atas laporan Anda. Tim pengelola UKM Pers SUKMA akan segera meninjau dan menindaklanjuti isu yang Anda sampaikan.
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card-custom">
            <div class="card-header-custom">
                <h5><i class="fa-regular fa-square-plus me-2 text-primary"></i>Formulir Pengaduan Isu</h5>
            </div>
            
            <div class="card-body-custom">
                <form method="POST" enctype="multipart/form-data">
                    <!-- Nama Pelapor -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pelapor</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-regular fa-user"></i></span>
                            <input type="text" name="nama_pelapor" class="form-control" placeholder="Masukkan nama Anda (atau tulis 'Anonim')..." required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Kategori Isu -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">Kategori Isu</label>
                            <select name="id_kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php
                                $kategori = mysqli_query($koneksi, "SELECT * FROM kategori_isu");
                                while ($k = mysqli_fetch_assoc($kategori)) {
                                    echo "<option value='{$k['id_kategori']}'>{$k['nama_kategori']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <!-- Prioritas -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold">Tingkat Urgensi / Prioritas</label>
                            <select name="prioritas" class="form-select">
                                <option value="Rendah">Rendah</option>
                                <option value="Sedang" selected>Sedang</option>
                                <option value="Tinggi">Tinggi</option>
                            </select>
                        </div>
                    </div>

                    <!-- Judul Isu -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Isu</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-regular fa-lightbulb"></i></span>
                            <input type="text" name="judul_isu" class="form-control" placeholder="Tuliskan judul isu atau topik laporan..." required>
                        </div>
                    </div>

                    <!-- Deskripsi Isu -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Isu / Masalah</label>
                        <textarea name="deskripsi" class="form-control" rows="5" placeholder="Jelaskan secara detail mengenai isu, lokasi kejadian, kronologi, serta dampak masalah..." required></textarea>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Foto Bukti / Lampiran (Opsional)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <div class="form-text extra-small text-muted">Format file gambar yang diperbolehkan (.jpg, .jpeg, .png). Maksimal 2MB.</div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end border-top pt-3">
                        <button type="submit" name="simpan" class="btn btn-primary-custom px-4" style="border-radius: 4px;">
                            <i class="fa-regular fa-paper-plane me-2"></i>Kirim Laporan
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
