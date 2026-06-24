<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Fetch Statistics
$totalIsu = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM isu")
)['total'];

$totalMenunggu = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM isu WHERE status_isu='Menunggu'")
)['total'];

$totalDiproses = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM isu WHERE status_isu='Diproses'")
)['total'];

$totalSelesai = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM isu WHERE status_isu='Selesai'")
)['total'];

// Filter & Search inputs
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($koneksi, $_GET['keyword']) : '';
$filterKategori = isset($_GET['id_kategori']) ? mysqli_real_escape_string($koneksi, $_GET['id_kategori']) : '';
$filterStatus = isset($_GET['status_isu']) ? mysqli_real_escape_string($koneksi, $_GET['status_isu']) : '';

// Build Query
$whereClause = [];
if ($keyword != '') {
    $whereClause[] = "(isu.nama_pelapor LIKE '%$keyword%' OR isu.judul_isu LIKE '%$keyword%' OR isu.deskripsi LIKE '%$keyword%')";
}
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

$pageTitle = "Dashboard Isu Kampus";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Success Alert -->
<?php if (isset($_GET['success'])) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="border-radius: 4px; background-color: #e6f4ea; color: #2e5a3c; border-color: #a7f3d0;">
        <i class="fa-solid fa-circle-check me-2"></i>
        <div>
            <strong>Laporan Berhasil Terkirim!</strong> Terima kasih atas laporan Anda. Tim pengelola UKM Pers SUKMA akan segera meninjau dan menindaklanjuti isu yang Anda sampaikan.
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Overview / Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- Total Issues -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-total p-4 d-flex align-items-center justify-content-between h-100">
            <div>
                <p class="text-muted mb-1 fw-medium small">Total Isu Kampus</p>
                <h3 class="m-0 fw-semibold text-dark"><?= $totalIsu; ?></h3>
            </div>
            <div class="stat-icon stat-icon-total">
                <i class="fa-regular fa-folder"></i>
            </div>
        </div>
    </div>
    <!-- Waiting Issues -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-menunggu p-4 d-flex align-items-center justify-content-between h-100">
            <div>
                <p class="text-muted mb-1 fw-medium small">Status Menunggu</p>
                <h3 class="m-0 fw-semibold text-dark"><?= $totalMenunggu; ?></h3>
            </div>
            <div class="stat-icon stat-icon-menunggu">
                <i class="fa-solid fa-ellipsis"></i>
            </div>
        </div>
    </div>
    <!-- Processed Issues -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-proses p-4 d-flex align-items-center justify-content-between h-100">
            <div>
                <p class="text-muted mb-1 fw-medium small">Sedang Diproses</p>
                <h3 class="m-0 fw-semibold text-dark"><?= $totalDiproses; ?></h3>
            </div>
            <div class="stat-icon stat-icon-proses">
                <i class="fa-solid fa-gears"></i>
            </div>
        </div>
    </div>
    <!-- Finished Issues -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-selesai p-4 d-flex align-items-center justify-content-between h-100">
            <div>
                <p class="text-muted mb-1 fw-medium small">Laporan Selesai</p>
                <h3 class="m-0 fw-semibold text-dark"><?= $totalSelesai; ?></h3>
            </div>
            <div class="stat-icon stat-icon-selesai">
                <i class="fa-regular fa-circle-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search, Filter & Action Section -->
<div class="card-custom mb-4">
    <div class="card-body-custom">
        <form method="GET" class="row g-3 align-items-end">
            <!-- Keyword Search -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium text-muted small">Cari Isu / Pelapor</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Ketik kata kunci..." value="<?= htmlspecialchars($keyword); ?>">
                </div>
            </div>

            <!-- Category Filter -->
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label fw-medium text-muted small">Kategori</label>
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
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label fw-medium text-muted small">Status</label>
                <select name="status_isu" class="form-select">
                    <option value="Semua" <?= $filterStatus == 'Semua' ? 'selected' : ''; ?>>-- Semua Status --</option>
                    <option value="Menunggu" <?= $filterStatus == 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                    <option value="Diproses" <?= $filterStatus == 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
                    <option value="Selesai" <?= $filterStatus == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                </select>
            </div>

            <!-- Submit Buttons -->
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom flex-grow-1">
                    <i class="fa-solid fa-filter me-2"></i>Filter
                </button>
                <?php if ($keyword != '' || $filterKategori != '' || ($filterStatus != '' && $filterStatus != 'Semua')) : ?>
                    <a href="index.php" class="btn btn-secondary-custom" title="Reset Filter">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Data Table Card -->
<div class="card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5 class="m-0"><i class="fa-regular fa-rectangle-list me-2 text-primary"></i>Daftar Laporan Isu Kampus</h5>
        <a href="tambah.php" class="btn btn-primary-custom">
            <i class="fa-solid fa-plus me-2"></i>Tambah Isu Baru
        </a>
    </div>
    <div class="card-body-custom p-0">
        <div class="table-responsive table-responsive-custom">
            <table class="table table-custom table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 18%;">Nama Pelapor</th>
                        <th style="width: 15%;">Kategori</th>
                        <th style="width: 25%;">Judul Isu</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 10%;">Prioritas</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($query) > 0) {
                        while ($data = mysqli_fetch_assoc($query)) {
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
                            <tr class="fade-in-up">
                                <td><?= $no++; ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($data['nama_pelapor'] ?: 'Anonim'); ?></td>
                                <td>
                                    <span class="text-plain-gray">
                                        <?= htmlspecialchars($data['nama_kategori']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($data['judul_isu']); ?>">
                                        <?= htmlspecialchars($data['judul_isu']); ?>
                                    </span>
                                </td>
                                <td>
                                    <i class="far fa-clock me-1 text-muted"></i>
                                    <?= date('d/m/Y', strtotime($data['tanggal_lapor'])); ?>
                                </td>
                                <td>
                                    <span class="text-plain-priority-sedang">
                                        <?= $priorityText; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-custom <?= $statusClass; ?> extra-small">
                                        <?= $statusText; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="detail.php?id=<?= $data['id_isu']; ?>" class="action-icon-btn action-icon-view" title="Detail">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                        <?php if (isset($_SESSION['login'])) : ?>
                                            <a href="edit.php?id=<?= $data['id_isu']; ?>" class="action-icon-btn action-icon-edit" title="Edit">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <a href="hapus.php?id=<?= $data['id_isu']; ?>" class="action-icon-btn action-icon-delete" onclick="return confirm('Yakin ingin menghapus laporan ini?')" title="Hapus">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-folder-open fa-3x mb-3 d-block text-light"></i>
                                Tidak ada data isu yang ditemukan.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>