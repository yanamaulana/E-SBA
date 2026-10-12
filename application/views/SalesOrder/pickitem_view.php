<?php

/**
 * APPLICATION : SAI ERP - Migration to CI3
 * FILENAME    : pickitem_view.php
 * DESCRIPTION : Pick Item with Full Customer Detail Parsing (Migration from CF10)
 */

// 1. TANGKAP PARAMETER
$cboCustomer = $this->input->get_post('cboCustomer') ?? '';
$selType     = $this->input->get_post('selType') ?? 'ItemCode';
$ExtraQuery  = $this->input->get_post('ExtraQuery') ?? '';
$selPage     = (int)($this->input->get_post('selPage') ?? 1);
$limit       = 10;
$offset      = ($selPage - 1) * $limit;
$companyID   = 2;
$strCatType  = 'FG'; // Kita kunci di FG sesuai request awal

// 2. QUERY LIST CUSTOMER (Untuk Dropdown)
$sqlCust = "
    SELECT Account_ID, AccountTitle_Code, Account_Code, Account_Name
    FROM TAccount
    WHERE $companyID IN (TAccount.company_id)
    AND TAccount.Cust_FG = 1
    AND TAccount.Category_ID IN (SELECT DISTINCT CATEGORY_ID FROM TDATAGROUPACCOUNT WHERE DATAGROUP_ID IN (20, 86))
    AND Status = '1' AND isnull(TAccount.Flag, 0) = 0
    ORDER BY Account_Name ASC
";
$qCustomer = $this->db->query($sqlCust)->result();

// Tambahkan Account_Code di baris SELECT-nya
$sqlDetail = "
    SELECT Account_id, Account_Code, TAccount.Account_Name, Account_CurrencyID, Account_Address1 as Addr, 
           PaymentTerms, isnull(PAYMENTTERM,0) as PAYMENTTERM, Selling_Price_Type,TaxFileNumber,
           isnull(kawasanberikat,0) as kawasanberikat, isnull(isSisterCompany,0) as isSisterCompany,
           cust_salesperson as salescode,
           isNull(P.First_Name,'') + ' ' + isNull(P.Middle_Name,'') + ' ' + isNull(P.Last_Name,'') AS salesname
    FROM TAccount
    LEFT JOIN THRMEMPPERSONALDATA P ON TAccount.Cust_SalesPerson = P.EMP_ID
    WHERE Account_ID = ?
";
$custDetail = $this->db->query($sqlDetail, [$cboCustomer])->row();


$contact = null;
if (!empty($cboCustomer)) {
    $sqlCP = "
        SELECT TOP 1 TContact.Contact_id, Contact_FirstName, Contact_MiddleName, Contact_LastName, Contact_HomeAddress as Addr
        FROM TContact
        INNER JOIN TAccountContact ON TContact.Contact_ID = TAccountContact.Contact_ID
        WHERE Account_id = ?
    ";
    $contact = $this->db->query($sqlCP, [$cboCustomer])->row();
}



// 3. LOGIC PENGAMBILAN DETAIL CUSTOMER (Jika Customer Terpilih)
$custDetail = null;
$discount   = null;
if (!empty($cboCustomer)) {
    // A. Query Detail Customer (qCustDetail)
    $sqlDetail = "
        SELECT Account_id, TAccount.Account_Name, Account_CurrencyID, Account_Address1 as Addr, 
               PaymentTerms, isnull(PAYMENTTERM,0) as PAYMENTTERM, Selling_Price_Type,
               isnull(kawasanberikat,0) as kawasanberikat, isnull(isSisterCompany,0) as isSisterCompany,
               cust_salesperson as salescode,
               isNull(P.First_Name,'') + ' ' + isNull(P.Middle_Name,'') + ' ' + isNull(P.Last_Name,'') AS salesname
        FROM TAccount
        LEFT JOIN THRMEMPPERSONALDATA P ON TAccount.Cust_SalesPerson = P.EMP_ID
        WHERE Account_ID = ?
    ";
    $custDetail = $this->db->query($sqlDetail, [$cboCustomer])->row();

    // B. Query Discount (qDiscount)
    $sqlDisc = "
        SELECT TOP 1 persen, disc_id
        FROM tdiscount
        WHERE account_id = ?
        AND effective_date < GETDATE() + 1
        AND type = 'Sales'
        ORDER BY effective_date DESC
    ";
    $discount = $this->db->query($sqlDisc, [$cboCustomer])->row();
}

// 4. LOGIC PENCARIAN ITEM (Seperti sebelumnya)
$sqlSearch = "";
if (!empty($ExtraQuery)) {
    $cleanQuery = str_replace("'", "''", $ExtraQuery);
    $sqlSearch = ($selType == 'ItemCode') ? " AND TITEM.item_code LIKE '%$cleanQuery%' " : " AND TITEM.Item_name LIKE '%$cleanQuery%' ";
}

// 5. HITUNG TOTAL & QUERY ITEM
$sqlCount = "SELECT COUNT(TITEM.item_code) as total FROM TITEM INNER JOIN TItemCompany ON TItemCompany.item_code = TItem.item_code INNER JOIN TitemCategory ON TitemCategory.ItemCategory_ID = TItemCompany.ItemCategory_ID WHERE (TItem.InActive is NULL Or TItem.InActive = 0) AND TItemCompany.Company_ID = $companyID AND TitemCategory.ItemCategoryType = '$strCatType' AND TITEM.itemclass = 0 AND TITEM.item_code IN (SELECT DISTINCT ITEM_CODE FROM TDATAGROUPITEM WHERE DATAGROUP_ID IN (20, 86)) $sqlSearch";
$totalRows = $this->db->query($sqlCount)->row()->total;
$totalPages = ceil($totalRows / $limit);

$sqlItem = "SELECT 
                TITEM.item_code, 
                TITEM.Item_name, 
                tgscolor.Color_Name as Color, 
                TITEM.Item_Size as Brand, 
                CAST(ISNULL(TITEM.Item_Length,0) AS VARCHAR) + ' x ' + CAST(ISNULL(TITEM.Item_Width,0) AS VARCHAR) + ' x ' + CAST(ISNULL(TITEM.Item_Height,0) AS VARCHAR) + ' mm' as Size, 
                TITEM.customfield1 AS Type,
                -- Tambahan sesuai instruksi Mas Yana
                TITEM.unit_type_id,
                (SELECT unit_name FROM taccunittype WHERE unit_type_id = TITEM.unit_type_id) AS unit_name,
                TItemCompany.Dimension_ID,
                    -- Menarik Unit_Type_ID2 dari query kedua
                (SELECT TOP 1 conv.Unit_Type_ID 
                FROM TItemQtyConvert conv 
                WHERE conv.Item_Code = TITEM.item_code 
                AND conv.Company_ID = TItemCompany.Company_ID 
                AND conv.Document_Type = 'SN') AS Unit_Type_ID2,
                
                -- Menarik Unit_Name2 dari query kedua
                (SELECT TOP 1 unit2.Unit_Name 
                FROM TItemQtyConvert conv 
                INNER JOIN TACCUNITTYPE unit2 ON unit2.Unit_Type_ID = conv.Unit_Type_ID
                WHERE conv.Item_Code = TITEM.item_code 
                AND conv.Company_ID = TItemCompany.Company_ID 
                AND conv.Document_Type = 'SN') AS Unit_Name2
            FROM TITEM 
            INNER JOIN TItemCompany ON TItemCompany.item_code = TItem.item_code 
            INNER JOIN TitemCategory ON TitemCategory.ItemCategory_ID = TItemCompany.ItemCategory_ID 
            INNER JOIN TItemDimension ON TItemDimension.Dimension_ID = TItemCompany.Dimension_ID 
            LEFT JOIN tgscolor ON tgscolor.color_code = TITEM.item_color 
            WHERE (TItem.InActive is NULL Or TItem.InActive = 0) 
            AND TItemCompany.Company_ID = $companyID 
            AND TitemCategory.ItemCategoryType = '$strCatType' 
            AND TITEM.itemclass = 0 
            AND TITEM.item_code IN (SELECT DISTINCT ITEM_CODE FROM TDATAGROUPITEM WHERE DATAGROUP_ID IN (20, 86)) 
            $sqlSearch 
            ORDER BY TITEM.item_code ASC 
            OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY";

$qItem = $this->db->query($sqlItem)->result();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pick Item - FG Sales</title>
    <link href="<?= base_url() ?>assets/Metronic/dist/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/Metronic/dist/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            font-size: 11px;
            font-family: 'Tahoma', sans-serif;
            overflow: hidden;
            background: #fff;
        }

        .header-blue {
            background: #000066;
            color: #fff;
            padding: 6px 10px;
            font-weight: bold;
            border-bottom: 2px solid #cc0000;
        }

        .filter-section {
            background: #f8f9fa;
            border-bottom: 1px solid #ccc;
            padding: 10px;
        }

        .table-area {
            height: 480px;
            overflow-y: auto;
            border: 1px solid #ccc;
        }

        thead th {
            position: sticky;
            top: 0;
            background: #eee !important;
            z-index: 10;
            border-bottom: 2px solid #ddd !important;
            text-align: center;
        }

        .footer-action {
            background: #eee;
            padding: 10px;
            border-top: 1px solid #ccc;
        }
    </style>
</head>

<body class="p-0" onload="self.focus();">

    <div class="header-blue">List of Item (Finished Goods)</div>

    <form name="frmSearch" id="frmSearch" method="post" action="<?= current_url() ?>">
                <?php $this->load->view('partials/csrf_field'); ?>
        <input type="hidden" name="selPage" id="selPage" value="<?= $selPage ?>">

        <input type="hidden" name="txtTerms" value="<?= $custDetail->PaymentTerms ?? '' ?>">
        <input type="hidden" name="txtTermsdate" value="<?= $custDetail->PAYMENTTERM ?? '0' ?>">
        <input type="hidden" name="hdnBZ" value="<?= $custDetail->kawasanberikat ?? '0' ?>">
        <input type="hidden" name="hdnDiscountId" value="<?= $discount->disc_id ?? '' ?>">
        <input type="hidden" name="hdnAccName" value="<?= htmlspecialchars($custDetail->Account_Name ?? '') ?>">
        <input type="hidden" name="hdnAccCode" value="<?= $custDetail->Account_id ?? '' ?>">
        <input type="hidden" name="hdnAccAddr" value="<?= htmlspecialchars($custDetail->Addr ?? '') ?>">
        <input type="hidden" name="hdnAccCurr" value="<?= $custDetail->Account_CurrencyID ?? 'IDR' ?>">
        <input type="hidden" name="hdnSalesName" value="<?= htmlspecialchars($custDetail->salesname ?? '') ?>">
        <input type="hidden" name="hdnSalesCode" value="<?= $custDetail->salescode ?? '' ?>">
        <input type="hidden" name="selCatType" value="<?= $strCatType ?>">
        <input type="hidden" name="hdnAccRealCode" value="<?= trim($custDetail->Account_Code ?? '') ?>">
        <input type="hidden" name="hdnTaxNumber" value="<?= $custDetail->TaxFileNumber ?? '' ?>">
        <?php
        $fullCPName = trim(($contact->Contact_FirstName ?? '') . ' ' . ($contact->Contact_MiddleName ?? '') . ' ' . ($contact->Contact_LastName ?? ''));
        ?>
        <input type="hidden" name="hdnCPName" value="<?= htmlspecialchars($fullCPName) ?>">
        <input type="hidden" name="hdnCPAddr" value="<?= htmlspecialchars($contact->Addr ?? '') ?>">
        <input type="hidden" name="hdnCPCode" value="<?= $contact->Contact_id ?? '' ?>">
        <div class="filter-section">
            <div class="row mb-2">
                <div class="col-3 text-end align-self-center">Customer :</div>
                <div class="col-9">
                    <select name="cboCustomer" id="cboCustomer" class="form-select form-select-sm" data-control="select2" onchange="resetPage()">
                        <option value="">-- Select Customer --</option>
                        <?php foreach ($qCustomer as $c): ?>
                            <option value="<?= $c->Account_ID ?>" <?= ($cboCustomer == $c->Account_ID) ? 'selected' : '' ?>>
                                <?php
                                $title = trim($c->AccountTitle_Code ?? '');
                                echo ($title !== '' ? $title . '. ' : '') . $c->Account_Name;
                                ?> [<?= trim($c->Account_Code ?? '') ?>]
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-3 text-end align-self-center">Search :</div>
                <div class="col-9">
                    <div class="input-group input-group-sm">
                        <select name="selType" class="form-select col-3">
                            <option value="ItemCode" <?= ($selType == 'ItemCode') ? 'selected' : '' ?>>Item Code</option>
                            <option value="ItemName" <?= ($selType == 'ItemName') ? 'selected' : '' ?>>Item Name</option>
                        </select>
                        <input type="text" name="ExtraQuery" class="form-control" value="<?= htmlspecialchars($ExtraQuery) ?>">
                        <button type="button" class="btn btn-primary" onclick="resetPage()">Search</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-3 text-end align-self-center">Page :</div>
                <div class="col-9">
                    <select class="form-select form-select-sm d-inline-block w-auto" onchange="$('#selPage').val(this.value); $('#frmSearch').submit();">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <option value="<?= $i ?>" <?= ($selPage == $i) ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                    <span class="ms-2 text-muted">Total: <?= $totalRows ?> items</span>
                </div>
            </div>
        </div>
        <div class="footer-action">
            <button type="button" class="btn btn-sm btn-primary px-4" onclick="selectalot('C')">Pick Customer & Item</button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="window.close()">Close</button>
        </div>
        <div class="px-2 mt-2">
            <div class="table-area">
                <table class="table table-sm table-bordered mb-0 text-nowrap">
                    <thead>
                        <tr>
                            <th width="30"><input type="checkbox" id="chkAll"></th>
                            <th width="40">No.</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Color</th>
                            <th>Brand</th>
                            <th>Size</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($qItem)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">-- No Record Found --</td>
                            </tr>
                            <?php else:
                            $no = $offset + 1;
                            foreach ($qItem as $row): ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="chkItem"
                                            value="<?= $row->item_code ?>"
                                            data-code="<?= $row->item_code ?>"
                                            data-name="<?= htmlspecialchars($row->Item_name) ?>"
                                            data-color="<?= $row->Color ?? '-' ?>"
                                            data-brand="<?= $row->Brand ?? '-' ?>"
                                            data-size="<?= $row->Size ?>"
                                            data-type="<?= $row->Type ?>"
                                            data-unitid="<?= $row->unit_type_id ?>"
                                            data-unitname="<?= $row->unit_name ?>"
                                            data-unitid2="<?= $row->Unit_Type_ID2 ?>"
                                            data-unitname2="<?= $row->Unit_Name2 ?>"
                                            data-dimid="<?= $row->Dimension_ID ?>">
                                    </td>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= $row->item_code ?></td>
                                    <td><?= $row->Item_name ?></td>
                                    <td class="text-center"><?= $row->Color ?? '-' ?></td>
                                    <td class="text-center"><?= $row->Brand ?? '-' ?></td>
                                    <td class="text-center"><?= $row->Size ?></td>
                                    <td><?= $row->Type ?></td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>


    </form>

    <script src="<?= base_url() ?>assets/Metronic/dist/assets/plugins/global/plugins.bundle.js"></script>
    <?php $this->load->view('partials/csrf_script'); ?>
    <script src="<?= base_url() ?>assets/Metronic/dist/assets/js/scripts.bundle.js"></script>

    <script>
        function resetPage() {
            $('#selPage').val(1);
            $('#frmSearch').submit();
        }
        $('#chkAll').click(function() {
            $('input[name="chkItem"]').prop('checked', this.checked);
        });

        function selectalot(meth) {
            if ($('#cboCustomer').val() == "") {
                alert("Please Select Customer!");
                return false;
            }
            // if ($('input[name="chkItem"]:checked').length == 0) {
            //     alert("Please Pick Item!");
            //     return false;
            // }

            if (window.opener && !window.opener.closed) {
                var custData = {
                    Account_ID: $('input[name="hdnAccCode"]').val(),
                    Account_Name: $('input[name="hdnAccName"]').val(),
                    Account_Code: $('input[name="hdnAccRealCode"]').val(), // Kode akun (misal: CS001)
                    Addr: $('input[name="hdnAccAddr"]').val(),
                    Account_CurrencyID: $('input[name="hdnAccCurr"]').val(),
                    PaymentTerms: $('input[name="txtTerms"]').val(),
                    PAYMENTTERM: $('input[name="txtTermsdate"]').val(),
                    kawasanberikat: $('input[name="hdnBZ"]').val(),
                    salesname: $('input[name="hdnSalesName"]').val(),
                    salescode: $('input[name="hdnSalesCode"]').val(),
                    disc_id: $('input[name="hdnDiscountId"]').val(),
                    taxNumber: $('input[name="hdnTaxNumber"]').val(),
                    cpName: $('input[name="hdnCPName"]').val(),
                    cpAddr: $('input[name="hdnCPAddr"]').val(),
                    cpCode: $('input[name="hdnCPCode"]').val(),
                    taxNumber: $('input[name="hdnTaxNumber"]').val()
                };

                window.opener.pickItemWindow = window;
                if (typeof window.opener.getItem === 'function') {
                    window.opener.getItem(meth, custData);
                    window.close();
                }
            }
        }
    </script>
</body>

</html>