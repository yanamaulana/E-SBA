<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }

        .header-container {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        form {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        input[type="date"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            padding: 9px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }

        button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            font-size: 13px;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #fdfdfd;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        .empty-state {
            margin-top: 20px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            text-align: center;
            color: #777;
        }
    </style>
</head>

<body>

    <div class="header-container">
        <!-- Form Pencarian Tanggal -->
        <!-- Pastikan action mengarah ke rute controller Anda -->
        <form action="<?= site_url('report/view_report') ?>" method="POST">
                <?php $this->load->view('partials/csrf_field'); ?>
            <label for="attendance_date"><strong>Pilih Tanggal Absensi:</strong></label>
            <input type="date" id="attendance_date" name="attendance_date" value="<?= isset($tanggal_h) ? $tanggal_h : date('Y-m-d') ?>" required>
            <button type="submit">View Report</button>
        </form>
    </div>

    <?php if (isset($report_data) && !empty($report_data)): ?>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">NO</th>
                    <th rowspan="2">NIP</th>
                    <th rowspan="2">NAMA</th>
                    <th rowspan="2">JABATAN</th>
                    <!-- Header Tanggal Dinamis dari Controller -->
                    <th colspan="2"><?= date('d-M-Y', strtotime($tanggal_h_min_1)) ?></th>
                    <th colspan="2"><?= date('d-M-Y', strtotime($tanggal_h)) ?></th>
                    <th rowspan="2">BAGIAN</th>
                    <th rowspan="2">DIVISI</th>
                </tr>
                <tr>
                    <th>MASUK</th>
                    <th>KELUAR</th>
                    <th>MASUK</th>
                    <th>KELUAR</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($report_data as $row): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="text-center"><?= $row['NIP'] ?></td>
                        <td><?= $row['NAMA'] ?></td>
                        <td><?= $row['JABATAN'] ?></td>
                        <td class="text-center"><?= !empty($row['MASUK_H_MIN_1']) ? $row['MASUK_H_MIN_1'] : '-' ?></td>
                        <td class="text-center"><?= !empty($row['KELUAR_H_MIN_1']) ? $row['KELUAR_H_MIN_1'] : '-' ?></td>
                        <td class="text-center"><?= !empty($row['MASUK_H']) ? $row['MASUK_H'] : '-' ?></td>
                        <td class="text-center"><?= !empty($row['KELUAR_H']) ? $row['KELUAR_H'] : '-' ?></td>
                        <td><?= $row['BAGIAN'] ?></td>
                        <td><?= $row['Divisi'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (isset($report_data) && empty($report_data)): ?>
        <div class="empty-state">
            <p>Tidak ada data absensi yang ditemukan untuk tanggal terpilih.</p>
        </div>
    <?php endif; ?>

</body>

</html>