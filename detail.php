<?php
session_start();

include 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);
$query = mysqli_query($koneksi, "
    SELECT isu.*, kategori_isu.nama_kategori 
    FROM isu 
    JOIN kategori_isu ON isu.id_kategori = kategori_isu.id_kategori 
    WHERE isu.id_isu = '$id'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: index.php");
    exit;
}

$pageTitle = "Detail Isu";
include 'includes/header.php';
include 'includes/sidebar.php';

// Status formatting
$statusText = $data['status_isu'] ?? 'Menunggu';
$statusClass = 'badge-status-menunggu';
if ($statusText == 'Diproses') {
    $statusClass = 'badge-status-diproses';
} elseif ($statusText == 'Selesai') {
    $statusClass = 'badge-status-selesai';
}

// Priority formatting
$priorityText = $data['prioritas'] ?? 'Sedang';
$priorityClass = 'badge-priority-sedang';
if ($priorityText == 'Tinggi') {
    $priorityClass = 'badge-priority-tinggi';
} elseif ($priorityText == 'Rendah') {
    $priorityClass = 'badge-priority-rendah';
}
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <!-- Navigation Breadcrumb-like Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-navy fw-bold m-0">
                <a href="index.php" class="text-decoration-none text-muted fw-normal"><i class="fa-solid fa-arrow-left me-2"></i>Dashboard</a> 
                <span class="text-muted mx-2">/</span> Detail Laporan
            </h4>
            <?php if (isset($_SESSION['login'])) : ?>
                <div class="d-flex gap-2">
                    <a href="edit.php?id=<?= $data['id_isu']; ?>" class="btn btn-warning-custom" style="border-radius: 4px;">
                        <i class="fa-regular fa-pen-to-square me-2"></i>Edit Laporan
                    </a>
                    <a href="hapus.php?id=<?= $data['id_isu']; ?>" class="btn btn-danger-custom" style="border-radius: 4px;" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                        <i class="fa-regular fa-trash-can me-2"></i>Hapus
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-custom shadow-sm mb-4">
            <div class="card-header-custom bg-white border-bottom py-3">
                <h5 class="fw-bold m-0"><i class="fa-regular fa-folder-open me-2 text-primary"></i>Informasi Isu #<?= $data['id_isu']; ?></h5>
            </div>
            <div class="card-body-custom p-4">
                <div class="row g-4">
                    <!-- Left Column: Details -->
                    <div class="col-12 col-md-7">
                        <h3 class="fw-bold text-navy mb-3"><?= htmlspecialchars($data['judul_isu']); ?></h3>
                        
                        <!-- Metadata Grid -->
                        <div class="row g-3 mb-4 py-3 bg-light rounded border border-light-subtle">
                            <div class="col-6 col-sm-4">
                                <span class="d-block text-muted small fw-medium">Nama Pelapor</span>
                                <span class="fw-semibold text-navy"><i class="fa-regular fa-user text-muted me-1 small"></i> <?= htmlspecialchars($data['nama_pelapor'] ?: 'Anonim'); ?></span>
                            </div>
                            <div class="col-6 col-sm-4">
                                <span class="d-block text-muted small fw-medium">Kategori Isu</span>
                                <span class="fw-semibold text-navy"><i class="fa-regular fa-bookmark text-muted me-1 small"></i> <?= htmlspecialchars($data['nama_kategori']); ?></span>
                            </div>
                            <div class="col-6 col-sm-4">
                                <span class="d-block text-muted small fw-medium">Tanggal Lapor</span>
                                <span class="fw-semibold text-navy"><i class="fa-regular fa-calendar text-muted me-1 small"></i> <?= date('d M Y', strtotime($data['tanggal_lapor'])); ?></span>
                            </div>
                            <div class="col-6 col-sm-4 mt-3">
                                <span class="d-block text-muted small fw-medium">Tingkat Prioritas</span>
                                <span class="text-plain-priority-sedang d-inline-block mt-1">
                                    <?= $priorityText; ?>
                                </span>
                            </div>
                            <div class="col-6 col-sm-4 mt-3">
                                <span class="d-block text-muted small fw-medium">Status Laporan</span>
                                <span class="badge-custom <?= $statusClass; ?> extra-small mt-1">
                                    <?= $statusText; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Description Box -->
                        <div class="mb-3">
                            <h6 class="fw-bold text-muted uppercase tracking-wide small">Deskripsi Laporan:</h6>
                            <div class="p-3 bg-white border rounded text-slate" style="white-space: pre-line; line-height: 1.6; font-size: 0.98rem; min-height: 150px;">
                                <?= htmlspecialchars($data['deskripsi']); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Evidence Photo -->
                    <div class="col-12 col-md-5">
                        <h6 class="fw-bold text-muted uppercase tracking-wide small mb-3">Foto Bukti / Lampiran:</h6>
                        <div class="text-center p-3 border rounded bg-light d-flex align-items-center justify-content-center" style="min-height: 250px;">
                            <?php 
                            $photoPath = 'uploads/' . $data['foto'];
                            if (!empty($data['foto']) && file_exists($photoPath)) { 
                            ?>
                                <div class="photo-viewer shadow-sm bg-white p-2 rounded">
                                    <img src="<?= $photoPath; ?>" class="img-fluid rounded" style="max-height: 350px; object-fit: contain;" alt="Foto Bukti">
                                    <div class="mt-2 small text-muted text-center">
                                        <a href="<?= $photoPath; ?>" target="_blank" class="text-decoration-none text-primary fw-medium">
                                            <i class="fa-solid fa-up-right-from-square me-1"></i> Lihat Ukuran Penuh
                                        </a>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="text-muted p-4">
                                    <i class="fa-regular fa-image fa-4x mb-3 text-secondary-light d-block"></i>
                                    <span>Tidak ada foto bukti dilampirkan.</span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light border-top py-3 text-end px-4">
                <a href="index.php" class="btn btn-secondary-custom">
                    <i class="fa-solid fa-chevron-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>