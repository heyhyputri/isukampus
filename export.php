<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Filter inputs
$filterKategori = isset($_GET['id_kategori']) ? mysqli_real_escape_string($koneksi, $_GET['id_kategori']) : '';
$filterStatus = isset($_GET['status_isu']) ? mysqli_real_escape_string($koneksi, $_GET['status_isu']) : '';

// Build Query
$whereClause = [];
if ($filterKategori != '') {
    $whereClause[] = "isu.id_kategori = '$filterKategori'";
}
if ($filterStatus != '' && $filterStatus != 'Semua') {
    $whereClause[] = "isu.status_isu = '$filterStatus'";
}

$queryStr = "SELECT * FROM isu JOIN kategori_isu ON isu.id_kategori = kategori_isu.id_kategori";
if (count($whereClause) > 0) {
    $queryStr .= " WHERE " . implode(" AND ", $whereClause);
}
$queryStr .= " ORDER BY isu.id_isu DESC";

$query = mysqli_query($koneksi, $queryStr);

$pageTitle = "Ekspor Laporan";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="text-navy fw-bold m-0"><i class="fa-regular fa-share-from-square me-2 text-primary"></i>Ekspor Laporan Isu</h4>
        <button onclick="window.print();" class="btn btn-primary-custom">
            <i class="fa-solid fa-print me-2"></i>Cetak Laporan (PDF)
        </button>
    </div>
</div>

<!-- Filter Box (Hidden on Print) -->
<div class="card-custom mb-4">
    <div class="card-header-custom">
        <h6 class="m-0 fw-bold"><i class="fa-regular fa-compass me-2 text-muted"></i>Filter Laporan Sebelum Diekspor</h6>
    </div>
    <div class="card-body-custom">
        <form method="GET" class="row g-3 align-items-end">
            <!-- Category Filter -->
            <div class="col-12 col-md-5">
                <label class="form-label fw-medium text-muted small">Kategori Isu</label>
                <select name="id_kategori" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    <?php
                    $kategoriQuery = mysqli_query($koneksi, "SELECT * FROM kategori_isu");
                    while ($kat = mysqli_fetch_assoc($kategoriQuery)) {
                        $selected = ($filterKategori == $kat['id_kategori']) ? 'selected' : '';
                        echo "<option value='{$kat['id_kategori']}' {$selected}>{$kat['nama_kategori']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-12 col-md-5">
                <label class="form-label fw-medium text-muted small">Status Laporan</label>
                <select name="status_isu" class="form-select">
                    <option value="Semua" <?= $filterStatus == 'Semua' ? 'selected' : ''; ?>>-- Semua Status --</option>
                    <option value="Menunggu" <?= $filterStatus == 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                    <option value="Diproses" <?= $filterStatus == 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
                    <option value="Selesai" <?= $filterStatus == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                </select>
            </div>

            <!-- Filter Trigger Button -->
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-secondary-custom w-100">
                    <i class="fa-solid fa-filter me-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Print Container Header (Visible on print preview, usually hidden on browser screen or styled simply) -->
<div class="d-none d-print-block text-center mb-5">
    <h2 class="fw-bold m-0 text-uppercase">LAPORAN DATA ISU KAMPUS</h2>
    <p class="text-muted m-0">Diekspor oleh Administrator pada tanggal <?= date('d M Y - H:i'); ?></p>
    <hr style="border: 2px solid #000; opacity: 1;" class="my-4">
</div>

<!-- Report Table Card -->
<div class="card-custom">
    <div class="card-body-custom p-0">
        <div class="table-responsive table-responsive-custom">
            <table class="table table-custom table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 10%;">ID Isu</th>
                        <th style="width: 20%;">Nama Pelapor</th>
                        <th style="width: 15%;">Kategori</th>
                        <th style="width: 25%;">Judul Isu</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 13%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($query) > 0) {
                        while ($data = mysqli_fetch_assoc($query)) {
                            $statusText = $data['status_isu'] ?? 'Menunggu';
                            $statusClass = 'badge-status-menunggu';
                            if ($statusText == 'Diproses') {
                                $statusClass = 'badge-status-diproses';
                            } elseif ($statusText == 'Selesai') {
                                $statusClass = 'badge-status-selesai';
                            }
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><strong>#<?= $data['id_isu']; ?></strong></td>
                                <td class="fw-semibold"><?= htmlspecialchars($data['nama_pelapor'] ?: 'Anonim'); ?></td>
                                <td><?= htmlspecialchars($data['nama_kategori']); ?></td>
                                <td><?= htmlspecialchars($data['judul_isu']); ?></td>
                                <td><?= date('d/m/Y', strtotime($data['tanggal_lapor'])); ?></td>
                                <td>
                                    <span class="badge-custom <?= $statusClass; ?> extra-small">
                                        <?= $statusText; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php 
                        } 
                    } else {
                        ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Tidak ada data isu yang sesuai filter.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-none d-print-block mt-5 text-end">
    <div class="d-inline-block text-center" style="width: 200px;">
        <p class="mb-5">Petugas Administrator,</p>
        <div style="border-bottom: 1px solid #000; width: 100%; height: 50px;"></div>
        <p class="mt-2 fw-semibold">Admin Isu Kampus</p>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
