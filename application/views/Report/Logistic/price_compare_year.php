<div class="row gx-5 gx-xl-10">
    <style>
        .table-condensed thead tr:nth-child(2),
        .table-condensed tbody {
            display: none
        }

        .monthselect {
            width: 100%;
        }

        .yearselect {
            width: 100%;
        }
    </style>
    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">
            <form action="" target="popup" id="FormReport" action="<?= base_url() ?>Report/Sales/Rpt_OstSO_v_Bom_v_StokMtrl" method="post">
                <?php $this->load->view('partials/csrf_field'); ?>
                <div class="card-body pt-0">
                    <div class="py-5">
                        <div class="row g-5 mt-5">
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Year :</label>
                                <div class="col-sm-2">
                                    <div class="input-group input-group-sm">
                                        <select type="text" name="year" id="year" class="form-control form-select text-center" placeholder="Year...">
                                            <?php
                                            $tahun_awal = date('Y');
                                            $tahun_akhir = 2010;
                                            for ($tahun = $tahun_awal; $tahun >= $tahun_akhir; $tahun--) {
                                                echo "<option value=\"$tahun\">$tahun</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- <div class="col-sm-2">
                                    <div class="input-group input-group-sm">
                                        <select type="text" name="month" id="month" class="form-control form-select" placeholder="month...">
                                            <php
                                            $bulan_nama = array(
                                                '01' => 'January',
                                                '02' => 'February',
                                                '03' => 'March',
                                                '04' => 'April',
                                                '05' => 'May',
                                                '06' => 'June',
                                                '07' => 'July',
                                                '08' => 'August',
                                                '09' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December'
                                            );
                                            foreach ($bulan_nama as $kode_bulan => $nama_bulan) {
                                                echo "<option value=\"$kode_bulan\">$nama_bulan</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-footer" style="border-top:solid #EFF2F5 2px;">
                <button type="button" class="btn btn-sm btn-light-danger float-start" id="Generate"><i class="fas fa-file"></i> Display Report</button>
                <a href="<?= base_url() ?>" class="btn btn-danger btn-sm float-end"><i class="far fa-arrow-alt-circle-left"></i> Cancel</a>
            </div>
        </div>
    </div>
</div>
<div id="location"></div>