<div class="card card-flush overflow-hidden h-xl-100">
    <form name="frmNew" id="frmNew" action="" method="post">
                <?php $this->load->view('partials/csrf_field'); ?>
        <div class="container-fluid py-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="SO_NUMBER">SO Number :</label>
                        <div class="col-sm-8">
                            <input type="text" id="SO_NUMBER" name="SO_NUMBER" class="form-control form-control-sm" placeholder="SOL209<?= date('ym') ?>-XXXXXXX" value="<?= $header->SO_Number ?? ''; ?>" readonly>
                            <input type="hidden" id="task" name="task" value="<?= $task ?>">
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtSOtype">So Tax Type :</label>
                        <div class="col-sm-8 pt-1">
                            <div class="custom-control custom-radio custom-control-inline custom-control-sm">
                                <input type="radio" name="txtSOtype" id="taxNormal" value="1" class="custom-control-input"
                                    <?= (isset($header->SOType) && $header->SOType == 1) || $task == 'new' ? 'checked' : '' ?>>
                                <label class="custom-control-label small" for="taxNormal">Normal</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline custom-control-sm">
                                <input type="radio" name="txtSOtype" id="taxVAT" value="0" class="custom-control-input"
                                    <?= (isset($header->SOType) && $header->SOType == 0) ? 'checked' : '' ?>>
                                <label class="custom-control-label small" for="taxVAT">VAT Include</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="selProject">Project Name :</label>
                        <?php if ($task == 'new') : ?>
                            <div class="col-sm-8">
                                <input type="text" id="selProject" name="selProject" class="form-control form-control-sm" placeholder="Project Name...">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">Allocate To :</label>
                        <div class="col-sm-8 pt-1">
                            <div class="custom-control custom-radio custom-control-inline custom-control-sm">
                                <input type="radio" name="rdoAllocate" id="rdoProj" value="0" class="custom-control-input"
                                    <?= (isset($rdoAllocate) && $rdoAllocate == 0) ? 'checked' : '' ?>>
                                <label class="custom-control-label small" for="rdoProj">Project Component</label>
                            </div>

                            <div class="custom-control custom-radio custom-control-inline custom-control-sm">
                                <input type="radio" name="rdoAllocate" id="rdoCC" value="1" class="custom-control-input"
                                    <?= (!isset($rdoAllocate) || $rdoAllocate == 1) ? 'checked' : 'checked' ?>>
                                <label class="custom-control-label small" for="rdoCC">Cost Center</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">Document Source :</label>
                        <div class="col-sm-8 pt-1">
                            <?php if ($task == 'new'): ?>
                                <div class="custom-control custom-radio custom-control-inline custom-control-sm">
                                    <input type="radio" name="rbTypedoc" id="docQuo" value="0" class="custom-control-input" checked>
                                    <label class="custom-control-label small" for="docQuo">Quotation</label>
                                </div>

                                <div class="custom-control custom-radio custom-control-inline custom-control-sm">
                                    <input type="radio" name="rbTypedoc" id="docPI" value="2" class="custom-control-input" disabled>
                                    <label class="custom-control-label small text-muted" for="docPI">Proforma Invoice</label>
                                </div>

                                <div class="custom-control custom-radio custom-control-inline custom-control-sm">
                                    <input type="radio" name="rbTypedoc" id="docSC" value="3" class="custom-control-input" disabled>
                                    <label class="custom-control-label small text-muted" for="docSC">Sales Contract</label>
                                </div>

                                <div class="mt-2">
                                    <div id="DivQuotation" class="mb-2">
                                        <input type="text" name="selQuotation" id="selQuotation"
                                            class="form-control form-control-sm"
                                            value="<?= $selQuotation ?>"
                                            placeholder="Input Quotation Number...">

                                        <?php if (!empty($SourceDate)): ?>
                                            <div class="mt-1 small text-danger font-italic">
                                                [Doc Source Date : <?= $SourceDate ?>]
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div id="DivProforma" class="mb-2" style="display: none;">
                                        <input type="text" name="selProforma" class="form-control form-control-sm bg-light"
                                            placeholder="Proforma Number (Disabled)" disabled>
                                    </div>

                                    <div id="DivSalesContract" style="display: none;">
                                        <input type="text" name="ddlSalesContract" class="form-control form-control-sm bg-light"
                                            placeholder="Contract Number (Disabled)" disabled>
                                    </div>
                                </div>
                            <?php else: ?>
                                -- No Quotation --
                                <input type="Hidden" name="SelQuotation" value="">
                                <input type="Hidden" name="SelPro" value="0">
                                <input type="Hidden" name="selProforma" value="">
                                <input type="Hidden" name="ddlSalesContract" value="">
                                <?php $SourceDate = ''; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtCustName">Customer * :</label>
                        <div class="col-sm-8">
                            <input type="text" name="txtCustName" id="txtCustName" class="form-control form-control-sm" placeholder="Customer...." readonly value="<?= $header->Account_Name ?? '' ?>">
                            <input type="hidden" name="txtCustCode" id="txtCustCode" value="<?= $header->Account_ID ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtCustAddress">Address :</label>
                        <div class="col-sm-8">
                            <textarea name="txtCustAddress" id="txtCustAddress" class="form-control form-control-sm" rows="3" placeholder="Address..." readonly><?= $header->account_address1 ?? '' ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm">Tax File Number :</label>
                        <div class="col-sm-8 pt-2">
                            <input type="hidden" name="txtnpwp" id="txtnpwp" value="<?= $header->TaxFileNumber ?? '' ?>" placeholder="Tax File Number..." readonly>
                            <span id="CPTaxFileNumber" class="fw-bold text-dark" style="font-size: 0.85rem;"><?= $header->TaxFileNumber ?? '-' ?></span>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtCPName">Contact Person :</label>
                        <div class="col-sm-8">
                            <input type="hidden" name="txtCPCode" id="txtCPCode" value="<?= $header->Contact_ID ?? '' ?>">
                            <input type="text" name="txtCPName" id="txtCPName" class="form-control form-control-sm" value="<?= $header->Contact_FirstName ?? '' ?>" placeholder="Contact Person..." readonly>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtCPAddress">Contact Address :</label>
                        <div class="col-sm-8">
                            <input type="text" name="txtCPAddress" class="form-control form-control-sm" value="<?= $header->Contact_HomeAddress ?? '' ?>" placeholder="Contact Address..." readonly>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtSPCode">Sales Person * :</label>
                        <div class="col-sm-8">
                            <?php if ($task == 'new') : ?>
                                <select name="txtSPCode" id="txtSPCode" class="form-control form-control-sm">
                                    <option value="">-- Select Sales Person --</option>
                                    <?php if (!empty($sales_person)): ?>
                                        <?php foreach ($sales_person as $sp): ?>
                                            <option value="<?= $sp->Emp_ID ?>"
                                                <?= (isset($header->Emp_ID) && $header->Emp_ID == $sp->Emp_ID) ? 'selected' : '' ?>>
                                                <?= $sp->name ?>
                                            </option>
                                        <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        <?php else: ?>
                            <select name="txtSPCode" id="txtSPCode" class="form-control form-control-sm">
                                <option value="<?= $header->Emp_ID ?>"><?= $header->Emp_name ?></option>
                            </select>
                        <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtMemo">Remarks :</label>
                        <div class="col-sm-8">
                            <textarea name="txtMemo" class="form-control form-control-sm" rows="3" placeholder="Remarks..."><?= $header->SO_Notes ?? '' ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm">Bonded Area :</label>
                        <div class="col-sm-8 pt-1">
                            <div class="custom-control custom-checkbox custom-control-sm">
                                <input type="checkbox" name="chkKawasan" id="chkKawasan" value="1" class="custom-control-input" <?= (isset($header->KawasanBerikat) && $header->KawasanBerikat == 1) ? 'checked' : '' ?>>
                                <label class="custom-control-label small" for="chkKawasan">Yes</label>
                            </div>
                        </div>
                    </div>

                    <?php
                    if ($task == 'edit') {
                        $qSnAccount = $this->db->query("SELECT Account_Id, Account_Name
                        from taccount
                        where account_id = '$header->sn_account_id'")->row();
                    }
                    ?>
                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="selSNGroup">SN Account :</label>
                        <div class="col-sm-8">
                            <select name="selSNGroup" class="form-control form-control-sm">
                                <?php if ($task == 'new') : ?>
                                    <option value="-1" disabled selected>NONE</option>
                                <?php else: ?>
                                    <option value="<?= $header->sn_account_id ?? '-1' ?>" disabled selected><?= $qSnAccount->Account_Name ?? 'NONE' ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <?php
                    if ($task == 'edit') {
                        $qSiAccount = $this->db->query("SELECT Account_Id, Account_Name
                        from taccount
                        where account_id = '$header->si_account_id'")->row();
                    }
                    ?>
                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="selSIGroup">SI Account :</label>
                        <div class="col-sm-8">
                            <select name="selSIGroup" class="form-control form-control-sm">
                                <?php if ($task == 'new') : ?>
                                    <option value="-1" disabled selected>NONE</option>
                                <?php else: ?>
                                    <option value="<?= $header->si_account_id ?? '-1' ?>" disabled selected><?= $qSiAccount->Account_Name ?? 'NONE' ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtRevisionReason">Revision Reason :</label>
                        <div class="col-sm-8">
                            <textarea name="txtRevisionReason" class="form-control form-control-sm" rows="3" placeholder="Revision Reason..."><?= $header->reason_revision ?? '' ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 border-left">
                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtSODate">SO Date :</label>
                        <div class="col-sm-8">
                            <input type="text" id="txtSODate" name="txtSODate" class="form-control form-control-sm date-picker" value="<?= isset($header->SO_Date) ? date('Y-m-d', strtotime($header->SO_Date)) : date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtInvDueDate">Due Date :</label>
                        <div class="col-sm-8">
                            <input type="text" id="txtInvDueDate" name="txtInvDueDate" class="form-control form-control-sm date-picker" value="<?= isset($header->Due_Date) ? date('Y-m-d', strtotime($header->Due_Date)) : date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtPONum">Cust PO Num :</label>
                        <div class="col-sm-8">
                            <input type="text" id="txtPONum" name="txtPONum" class="form-control form-control-sm" placeholder="Customer PO Number..." value="<?= $header->PO_NumCustomer ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtPODate">Cust PO Date :</label>
                        <div class="col-sm-8">
                            <input type="text" id="txtPODate" name="txtPODate" class="form-control form-control-sm date-picker" value="<?= isset($header->PO_DateCustomer) ? date('Y-m-d', strtotime($header->PO_DateCustomer)) : date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm">Prod Month/Year :</label>
                        <div class="col-sm-8">
                            <div class="row no-gutters">
                                <div class="col-6 pr-1">
                                    <select name="txtProMonth" id="txtProMonth" class="form-control form-control-sm">
                                        <option value="" <?= ($header->Production_month ?? '') == '' ? 'selected' : '' ?> disabled>-- Month --</option>
                                        <option value="1" <?= ($header->Production_month ?? '') == '1' ? 'selected' : '' ?>>January</option>
                                        <option value="2" <?= ($header->Production_month ?? '') == '2' ? 'selected' : '' ?>>February</option>
                                        <option value="3" <?= ($header->Production_month ?? '') == '3' ? 'selected' : '' ?>>March</option>
                                        <option value="4" <?= ($header->Production_month ?? '') == '4' ? 'selected' : '' ?>>April</option>
                                        <option value="5" <?= ($header->Production_month ?? '') == '5' ? 'selected' : '' ?>>May</option>
                                        <option value="6" <?= ($header->Production_month ?? '') == '6' ? 'selected' : '' ?>>June</option>
                                        <option value="7" <?= ($header->Production_month ?? '') == '7' ? 'selected' : '' ?>>July</option>
                                        <option value="8" <?= ($header->Production_month ?? '') == '8' ? 'selected' : '' ?>>August</option>
                                        <option value="9" <?= ($header->Production_month ?? '') == '9' ? 'selected' : '' ?>>September</option>
                                        <option value="10" <?= ($header->Production_month ?? '') == '10' ? 'selected' : '' ?>>October</option>
                                        <option value="11" <?= ($header->Production_month ?? '') == '11' ? 'selected' : '' ?>>November</option>
                                        <option value="12" <?= ($header->Production_month ?? '') == '12' ? 'selected' : '' ?>>December</option>
                                    </select>
                                </div>
                                <div class="col-6 pl-1">
                                    <select name="txtProYear" id="txtProYear" class="form-control form-control-sm">
                                        <option value="" <?= ($header->Production_year ?? '') == '1' ? 'selected' : '' ?> disabled>-- Year --</option>
                                        <?php
                                        $startYear   = 2024;
                                        $currentYear = (int)date('Y');
                                        $endYear     = $currentYear + 2;
                                        $savedYear = $this->input->post('txtProYear') ?? ($qSales->pro_year ?? "");
                                        for ($y = $startYear; $y <= $endYear; $y++) :
                                        ?>
                                            <?php if ($task == 'new') : ?>
                                                <option value="<?= $y ?>" <?= ($savedYear == $y) ? 'selected' : '' ?>>
                                                    <?= $y ?>
                                                </option>
                                            <?php else: ?>
                                                <option value="<?= $y ?>" <?= ($header->Production_year ?? '') == $y ? 'selected' : '' ?>>
                                                    <?= $y ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="selCurrency">SO Currency :</label>
                        <div class="col-sm-8">
                            <select name="selCurrency" id="selCurrency" class="form-control form-control-sm">
                                <?php if ($task == 'new') : ?>
                                    <option value="IDR" selected>IDR</option>
                                    <option value="AUD">AUD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                    <option value="JPY">JPY</option>
                                    <option value="KRW">KRW</option>
                                    <option value="SGD">SGD</option>
                                    <option value="USD">USD</option>
                                <?php else: ?>
                                    <option value="IDR" <?= ($header->Currency_ID ?? '') == 'IDR' ? 'selected' : '' ?>>IDR</option>
                                    <option value="AUD" <?= ($header->Currency_ID ?? '') == 'AUD' ? 'selected' : '' ?>>AUD</option>
                                    <option value="EUR" <?= ($header->Currency_ID ?? '') == 'EUR' ? 'selected' : '' ?>>EUR</option>
                                    <option value="GBP" <?= ($header->Currency_ID ?? '') == 'GBP' ? 'selected' : '' ?>>GBP</option>
                                    <option value="JPY" <?= ($header->Currency_ID ?? '') == 'JPY' ? 'selected' : '' ?>>JPY</option>
                                    <option value="KRW" <?= ($header->Currency_ID ?? '') == 'KRW' ? 'selected' : '' ?>>KRW</option>
                                    <option value="SGD" <?= ($header->Currency_ID ?? '') == 'SGD' ? 'selected' : '' ?>>SGD</option>
                                    <option value="USD" <?= ($header->Currency_ID ?? '') == 'USD' ? 'selected' : '' ?>>USD</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="selTaxCurrency">Tax Currency :</label>
                        <div class="col-sm-8">
                            <select name="selTaxCurrency" id="selTaxCurrency" class="form-control form-control-sm">
                                <?php if ($task == 'new') : ?>
                                    <option value="IDR" selected="">IDR</option>
                                    <option value="USD">USD</option>
                                <?php else: ?>
                                    <option value="IDR" <?= ($header->Tax_CurrID ?? '') == 'IDR' ? 'selected' : '' ?>>IDR</option>
                                    <option value="USD" <?= ($header->Tax_CurrID ?? '') == 'USD' ? 'selected' : '' ?>>USD</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="BaseCurr">Base Currency :</label>
                        <div class="col-sm-8">
                            <select name="BaseCurr" id="BaseCurr" class="form-control form-control-sm">
                                <option value="IDR" selected="">IDR</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="cboTerms">Payment Schedule :</label>
                        <div class="col-sm-8">
                            <input type="hidden" name="txtTerms" value="0">
                            <select name="cboTerms" id="cboTerms" class="form-control form-control-sm" onchange="lpage();">
                                <option value="NONE" selected="">NONE - 1 monthly (cash)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="cboTermsNew">Payment Terms :</label>
                        <div class="col-sm-8">
                            <input type="hidden" name="txtTermsNew">
                            <select name="cboTermsNew" id="cboTermsNew" class="form-control form-control-sm" onchange="lpage();">
                                <?php if ($task == 'new') : ?>
                                    <option value="" selected="">None - 1 month (default)</option>
                                    <option value="Term001">Cash - 30 days</option>
                                <?php else: ?>
                                    <option value="" <?= ($header->paymentterm_code ?? '') == '' ? 'selected' : '' ?>>None - 1 month (default)</option>
                                    <option value="Term001" <?= ($header->paymentterm_code ?? '') == 'Term001' ? 'selected' : '' ?>>Cash - 30 days</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtDeliveryTerms">Delivery Terms :</label>
                        <div class="col-sm-8">
                            <textarea name="txtDeliveryTerms" class="form-control form-control-sm" rows="2" placeholder="Delivery Terms..."><?= $header->Delivery_Terms ?? '' ?></textarea>
                        </div>
                    </div>

                    <!-- <div class="card mb-3 border-secondary">
                    <div class="card-header py-1 font-weight-bold small bg-light">Credit Info</div>
                    <div class="card-body p-2" style="font-size: 0.8rem;">
                        <input type="hidden" name="txtcreditlimit" value="0"><input type="hidden" name="baseCreditLimit" value="0">
                        <input type="hidden" name="txtInvNotPaid" value="4848785.666879991"><input type="hidden" name="baseInvNotPaid" value="46703772557.118">
                        <input type="hidden" name="txtSOApproved" value="55113214.79497244"><input type="hidden" name="baseSOApproved" value="530853542621.58">
                        <input type="hidden" name="txtRemainingCredit" value="-59962000.46185243"><input type="hidden" name="baseRemainCredit" value="-577557315178.698">

                        <div>Limit : <span id="idcreditLimit">(USD)</span> <span id="creditLimit">0</span></div>
                        <div>Inv Not Paid : <span id="idInvNotPaid">(USD)</span> <span id="InvNotPaid">4.848.785,66</span></div>
                        <div class="border-bottom pb-1">SO Approved : <span id="idSOApproved">(USD)</span> <span id="SOApproved">55.113.214,79</span></div>
                        <div class="pt-1">
                            Rem. Credit :
                            <a href="javascript://" onclick="arrNewPop[arrNewPop.length]=PopWindow('/samickerp/...','Preview','800','600','...');" class="font-weight-bold text-danger">
                                <span id="idRemainingCredit">(USD)</span> <span id="RemainingCredit">-59.962.000,46</span>
                            </a>
                        </div>
                    </div>
                </div> -->

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="cboPriceType">Price Type :</label>
                        <div class="col-sm-8">
                            <select name="cboPriceType" id="cboPriceType" class="form-control form-control-sm">
                                <?php if ($task == 'new') : ?>
                                    <option value="FOB" selected="">Freight on Board</option>
                                    <option value="CIF">Cost Insurance Freight</option>
                                    <option value="CFR">Cost and Freight</option>
                                <?php else: ?>
                                    <option value="FOB" <?= ($header->PriceType ?? '') == 'FOB' ? 'selected' : '' ?>>Freight on Board</option>
                                    <option value="CIF" <?= ($header->PriceType ?? '') == 'CIF' ? 'selected' : '' ?>>Cost Insurance Freight</option>
                                    <option value="CFR" <?= ($header->PriceType ?? '') == 'CFR' ? 'selected' : '' ?>>Cost and Freight</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mt-2 mx-4" style="border-bottom: solid #B5B5C3 1px;">
                        <label class="col-sm-4 col-form-label col-form-label-sm" for="txtPiNumber">Pi Number * :</label>
                        <div class="col-sm-8">
                            <input name="txtPiNumber" id="txtPiNumber" class="form-control form-control-sm" value="<?= $header->pi_number ?? '' ?>" placeholder="PI Number...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-3">
                    <fieldset class="border p-2">
                        <legend class="w-auto px-2 font-weight-bold small text-uppercase">Currency Converter</legend>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0" id="tblCurrConverter">
                            </table>
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-3">
                    <fieldset class="border p-2">
                        <legend class="w-auto px-2 font-weight-bold small text-uppercase">Tax Converter</legend>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0" id="tblTaxConverter">
                            </table>
                        </div>
                    </fieldset>
                </div>
            </div>
            <input type="hidden" name="CurrencyRateList" id="CurrencyRateList" value="" <?= $header->CurrencyRateList ?? '' ?>>

            <input type="hidden" name="txtCurr_<?= $_COOKIE['currencyid'] ?? 'IDR' ?>" value="1">
            <input type="hidden" name="txtTax_<?= $_COOKIE['currencyid'] ?? 'IDR' ?>" value="1">
            <div class="row mt-2">
                <div class="col-6">
                    <button type="button" id="btnPickItem" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus-circle"></i> [+] Pick Customer & Select Item Manually
                    </button>

                    <button type="button" id="btnRemoveItem" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-minus-circle"></i> [-] Remove Item
                    </button>
                </div>
                <div class="col-6 text-end">
                    <button type="button" class="btn btn-sm btn-light-danger" data-bs-toggle="modal" data-bs-target="#kt_modal_pajak">
                        <i class=" fas fa-dollar-sign"></i>[!] Kode Pajak
                    </button>
                    <button type="button" class="btn btn-sm btn-light-info" data-bs-toggle="modal" data-bs-target="#kt_modal_cc">
                        <i class="fas fa-bookmark"></i>[!] Kode CostCenter
                    </button>
                    <button type="button" id="btnUploadItem" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel"></i> [+] Upload Item
                    </button>
                </div>
            </div>

            <input type="hidden" name="hdnLstItemID" id="hdnLstItemID" value="">
            <div class="row mt-3">
                <div class="col-12 border p-0 shadow-sm" style="height: 480px; overflow:auto;">
                    <div class="table-responsive-lg">
                        <table class="table table-sm table-striped border mb-0 text-center" id="tbl_ID" style="font-size: 0.8rem;">
                            <thead style="background-color: #3B6D8C;">
                                <tr class="text-nowrap font-weight-bold text-white">
                                    <th>&nbsp;&nbsp;&nbsp;<input type="Checkbox" name="chkAll" id="chkAll" class="form-check-input"></th>
                                    <th>Item Code</th>
                                    <th>Description</th>
                                    <th>Notes</th>
                                    <th style="display:none">Dim</th>
                                    <th>Color</th>
                                    <th>Brand</th>
                                    <th>Type</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Qty 2</th>
                                    <th>Unit 2</th>
                                    <th style="display:none">Res Qty</th>
                                    <th style="display:none">Unit</th>
                                    <th>Unit Price <br><span id="idUnitPrice" class="badge badge-info small">Conv</span></th>
                                    <th>Disc Val</th>
                                    <th>Disc%</th>
                                    <th>Amount <br><span id="idAmount" class="badge badge-info small">Conv</span></th>
                                    <th>Tax 1</th>
                                    <th>Tax 2</th>
                                    <th colspan="2">Est. Date</th>
                                    <th>CC</th>
                                </tr>
                            </thead>
                            <tbody id="detail_body">
                                <?php if ($task == 'edit' && !empty($details)): ?>
                                    <?php foreach ($details as $i => $row): ?>
                                        <tr class="text-nowrap">
                                            <td class="text-center">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="chk_item[]" class="form-check-input"></td>

                                            <td>
                                                <?= $row->Item_Code ?>
                                                <input type="hidden" name="sodetail_id[]" value="<?= $row->SODEtail_ID ?>">
                                                <input type="hidden" name="item_code[]" value="<?= $row->Item_Code ?>">
                                                <input type="hidden" name="unit_id[]" value="<?= $row->Unit_Type ?>">
                                                <input type="hidden" name="unit_id2[]" value="<?= $row->Unit_Type2 ?>">
                                                <input type="hidden" name="dim_id[]" value="<?= $row->Dimension_ID ?>">
                                            </td>

                                            <td class="text-left">
                                                <?= $row->Item_Description ?>
                                                <input type="hidden" name="item_name[]" value="<?= $row->Item_Description ?>">
                                                <input type="hidden" name="gen_flag[]" value="<?= $row->generate_flag ?>">
                                                <input type="hidden" name="parent_item[]" value="<?= $row->parent_item ?>">
                                                <input type="hidden" name="parent_path[]" value="<?= $row->parent_path ?>">
                                            </td>

                                            <td style="min-width: 120px;">
                                                <input type="text" name="notes[]" class="form-control form-control-sm" value="<?= $row->Notes ?>">
                                            </td>

                                            <td style="display:none"><?= $row->SizeInfo ?></td>
                                            <td><?= $row->Item_Color ?></td>
                                            <td><?= $row->Item_Size ?></td>
                                            <td><?= $row->Type ?></td>

                                            <td style="min-width: 85px;">
                                                <input type="number" name="qty[]" class="form-control form-control-sm text-right qty-trigger" value="<?= (float)$row->Qty ?>">
                                            </td>
                                            <td class="fw-bold text-muted"><?= $row->Unit_Desc ?></td>

                                            <td style="min-width: 85px;">
                                                <input type="number" name="qty2[]" class="form-control form-control-sm text-right bg-light" readonly value="<?= (float)$row->Qty2 ?>">
                                            </td>
                                            <td class="fw-bold text-muted"><?= $row->Unit_Desc2 ?></td>

                                            <td style="display:none">
                                                <input type="hidden" name="cs_number[]" value="<?= $row->CS_Number ?>">
                                                <input type="hidden" name="extra_price[]" value="<?= (float)$row->ExtraPrice ?>">
                                                <input type="hidden" name="others[]" value="<?= $row->Others ?>">
                                            </td>

                                            <td style="min-width: 85px;">
                                                <input type="text" name="price[]" class="form-control form-control-sm text-right price-trigger" value="<?= number_format($row->UnitPrice, 2) ?>">
                                            </td>
                                            <td style="min-width: 85px;">
                                                <input type="text" name="disc_val[]" class="form-control form-control-sm text-right disc-trigger" value="<?= number_format($row->Disc_Value, 2) ?>">
                                            </td>
                                            <td style="min-width: 85px;">
                                                <input type="text" name="disc_pct[]" class="form-control form-control-sm text-right disc-pct-trigger" value="<?= (float)$row->Disc_percentage ?>">
                                            </td>

                                            <td class="text-right fw-bold total-amount"><?= number_format($row->TotalPrice, 2) ?></td>

                                            <td style="min-width: 110px;">
                                                <select name="tax1[]" class="form-control form-control-sm tax-trigger">
                                                    <option value="0">-- No Tax --</option>
                                                    <?php foreach ($tax_list as $tax): ?>
                                                        <option value="<?= $tax->Tax_Code ?>"
                                                            data-rate="<?= $tax->Tax_Rate ?>"
                                                            data-op="<?= $tax->Tax_operator ?>"
                                                            <?= ($row->Tax_Code1 == $tax->Tax_Code) ? 'selected' : '' ?>>
                                                            <?= $tax->Tax_Name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>

                                            <td style="min-width: 110px;">
                                                <select name="tax2[]" class="form-control form-control-sm tax-trigger">
                                                    <option value="0">-- No Tax --</option>
                                                    <?php foreach ($tax_list as $tax): ?>
                                                        <option value="<?= $tax->Tax_Code ?>"
                                                            data-rate="<?= $tax->Tax_Rate ?>"
                                                            data-op="<?= $tax->Tax_operator ?>"
                                                            <?= ($row->Tax_Code2 == $tax->Tax_Code) ? 'selected' : '' ?>>
                                                            <?= $tax->Tax_Name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>

                                            <td colspan="2" style="min-width: 110px;">
                                                <input type="text" name="est_date[]" class="form-control form-control-sm date-picker" value="<?= date('Y-m-d', strtotime($row->EstimateDate)) ?>">
                                            </td>

                                            <td style="min-width: 110px;">
                                                <select name="cc[]" class="form-control form-control-sm select2-item">
                                                    <option value="">-- No CC --</option>
                                                    <?php foreach ($cc_list as $cc): ?>
                                                        <option value="<?= $cc->Comp_ID ?>" <?= ($row->Comp_ID == $cc->Comp_ID) ? 'selected' : '' ?>>
                                                            <?= $cc->Comp_Name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border mt-4">
                        <div class="card-header py-2">
                            <h6 class="card-title mb-0 small text-uppercase font-weight-bold">Payment Detail</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-striped border mb-0" id="tblPayment">
                                <thead style="background-color: #3B6D8C;">
                                    <tr class="fw-bold fs-7 text-white">
                                        <th class="text-center" width="50">No</th>
                                        <th class="text-center">Invoice Date</th>
                                        <th class="text-center">Due Date</th>
                                        <th class="text-center">Amount (<span class="curr-label">USD</span>)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($task == 'new') : ?>
                                        <tr>
                                            <td class="text-center align-middle">1</td>
                                            <td>
                                                <input type="text" name="txtInvoiceDate1" class="form-control text-center form-control-sm date-picker" value="<?= date('Y-m-d') ?>">
                                            </td>
                                            <td>
                                                <input type="text" name="txtDueDate1" class="form-control text-center form-control-sm date-picker" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                                            </td>
                                            <td>
                                                <input type="text" name="txtAmount1" id="txtAmount1" class="form-control form-control-sm text-end bg-light" readonly value="0.0000">
                                                <input type="hidden" name="hidPercentage1" id="hidPercentage1" value="100">
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php
                                        $i = 1;
                                        $sql = $this->db->get_where('TACCCUSTOMERPAYMENT', ['TRX_NUMBER' => $header->SO_Number])->result();
                                        ?>
                                        <?php foreach ($sql as $row) : ?>
                                            <!-- TRX_NUMBER,DOC_TYPE,COMPANY_ID,PAYMENT_PERIOD,DUE_DATE,PAYDATE,AMOUNT,PAID_AMOUNT,UPDATED_BY,LAST_UPDATE,TOP_CODE,PRINT_DATE,PAYMENT_STATUS,INVOICE_DATE -->
                                            <tr>
                                                <td class="text-center align-middle"><?= $i++ ?></td>
                                                <td>
                                                    <input type="text" name="txtInvoiceDate1" class="form-control text-center form-control-sm date-picker" value="<?= date('Y-m-d', strtotime($row->INVOICE_DATE)) ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="txtDueDate1" class="form-control text-center form-control-sm date-picker" value="<?= date('Y-m-d', strtotime($row->DUE_DATE)) ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="txtAmount1" id="txtAmount1" class="form-control form-control-sm text-end bg-light" readonly value="<?= number_format($row->AMOUNT, 4) ?>">
                                                    <input type="hidden" name="hidPercentage1" id="hidPercentage1" value="100">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-1">&nbsp;</div> -->
                <div class="col-md-6 mt-4 pl-5">
                    <div class="card shadow-sm border">
                        <div class="card-body p-4">
                            <table class="table table-sm table-borderless mb-0" style="font-size: 0.85rem;">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold" width="40%">Total Qty</td>
                                        <td width="5%">:</td>
                                        <td>
                                            <input type="text" name="txtTotQty" id="txtTotQty" class="form-control form-control-sm text-end fw-bold bg-light" readonly value="0.0000">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="fw-bold">Total Amount (<span class="curr-label">Converted</span>)</td>
                                        <!-- sesuai SO Currency/ Sel Currency -->
                                        <td>:</td>
                                        <td>
                                            <input type="text" name="txtTotAmount" id="txtTotAmount" class="form-control form-control-sm text-end bg-light" readonly value="0.0000">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="fw-bold">
                                            Disc. Global
                                            <div class="input-group input-group-sm d-inline-flex ms-2" style="width: 70px;">
                                                <input type="number" id="idDiscall" name="txtDisctotal" class="form-control text-center p-1" value="0" max="100" oninput="if(value>100)value=100">
                                                <span class="input-group-text p-1">%</span>
                                            </div>
                                        </td>
                                        <td>:</td>
                                        <td>
                                            <input type="text" name="txtTotDisc" id="idTotalDiscall" class="form-control form-control-sm text-end bg-light" readonly value="0.0000">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="fw-bold">Total Tax (+ IDR)</td>
                                        <td>:</td>
                                        <td>
                                            <input type="text" name="txtTotTaxConv" id="txtTotTaxConv" class="form-control form-control-sm text-end bg-light text-primary" readonly value="0.0000">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="fw-bold">Total Deduction (- IDR)</td>
                                        <td>:</td>
                                        <td>
                                            <input type="text" name="txtTotDeductConv" id="txtTotDeductConv" class="form-control form-control-sm text-end bg-light text-danger" readonly value="0.0000">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="fw-bold text-danger">Claim Deduction (<span class="curr-label">IDR</span>)</td>
                                        <td>:</td>
                                        <td>
                                            <input type="text" name="txt_cd_amount" id="txt_cd_amount" class="form-control form-control-sm text-end border-danger" value="0.00">
                                        </td>
                                    </tr>

                                    <tr id="idTaxHide2" style="display: none;" class="border-top">
                                        <td class="fw-bold fs-6 text-primary py-3">Grand Total (<span class="curr-label">Converted</span>)</td>
                                        <td class="py-3">:</td>
                                        <td class="py-3">
                                            <input type="text" name="txtGrandTotal" id="txtGrandTotal" class="form-control form-control-sm text-end fw-bolder fs-6 border-primary bg-primary text-white" readonly value="0.0000">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="align-top pt-2">Claim Description</td>
                                        <td class="align-top pt-2">:</td>
                                        <td>
                                            <textarea name="txt_cd_desc" id="txt_cd_desc" class="form-control form-control-sm" rows="2" placeholder="Alasan pemotongan..."></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                // Siapkan variabel pendukung 
                // select * from TAccSetting
                $qSetting       = $this->db->get('TAccSetting')->row();
                $so_status       = isset($header->SO_Status) ? (int)$header->SO_Status : 0;
                $revision_number = isset($header->Revision_Number) ? (int)$header->Revision_Number : 0;
                $enable_revision = isset($qSetting->EnableSORevision) ? $qSetting->EnableSORevision : 1;
                ?>

                <table class="table">
                    <tr>
                        <td colspan="2" class="border-top py-3">
                            <div class="d-flex align-items-center gap-2">

                                <?php if ($task == 'new' || ($task == 'edit' && $so_status < 2)): ?>
                                    <button type="button" id="btnSubmit" name="btnSubmit" class="btn btn-primary px-4" onclick="passingVars();">
                                        <i class="fas fa-save mr-1"></i> <?= ($task == 'edit') ? 'Update' : 'Save' ?>
                                    </button>

                                    <button type="button" id="btnConfirm" name="btnConfirm" class="btn btn-success px-4" onclick="passingVars('YES');">
                                        <i class="fas fa-check-circle mr-1"></i> Confirm
                                    </button>
                                <?php endif; ?>


                                <?php if ($task == 'edit' && $so_status == 3 && $enable_revision == 1): ?>
                                    <input type="hidden" name="hidRevision" value="<?= $revision_number + 1 ?>">

                                    <button type="button" id="btnRevise" name="btnRevise" class="btn btn-warning px-4">
                                        <i class="fas fa-edit mr-1"></i> Revise
                                    </button>
                                <?php endif; ?>


                                <a class="btn btn-danger px-4" href="<?= base_url('SalesOrder/index') ?>">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </a>

                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>
</div>
<div class="modal fade" id="modalUploadExcel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel"></i> Upload Item Detail</h5>
                <div class="btn btn-icon btn-sm btn-light ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-2x">
                        <i class="fas fa-times"></i>
                    </span>
                </div>
            </div>
            <div class="modal-body">
                <label>Pilih File Excel (.xlsx)</label>
                <div class="form-group">
                    <input type="file" id="excel_file" class="form-control form-control-file" accept=".xlsx, .xls">
                </div>
                <div class="alert alert-info small mt-4">
                    <i class="fas fa-info-circle"></i> Gunakan template yang tersedia agar format data sesuai.
                    <a href="<?= base_url('assets/templates/SOtemplate.xlsx') ?>" id="downloadTemplate" class="font-weight-bold text-primary">Download Template</a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnExecuteUpload" class="btn btn-success">
                    <i class="fas fa-upload"></i> Proses Detail
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="kt_modal_pajak" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">List Kode Pajak Untuk Tax 1 & Tax 2</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-striped mb-0 gy-3 gs-3" style="font-size: 0.85rem;" id="table_tax">
                    <thead>
                        <tr class="fw-bold fs-6 text-white border-bottom border-gray-200" style="background-color: #3B6D8C;">
                            <th class="text-center">Kode <i class="fas fa-file-excel"></i></th>
                            <th class="text-center">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($tax_list as $tax): ?>
                            <tr>
                                <td class="text-center"><?= $tax->Tax_Code ?></td>
                                <td><?= $tax->Tax_Name ?>
                            </tr>

                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="kt_modal_cc" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">List Kode Cost Center</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-striped mb-0 gy-3 gs-3" style="font-size: 0.85rem;" id="table_cc">
                    <thead>
                        <tr class="fw-bold fs-6 text-white border-bottom border-gray-200" style="background-color: #3B6D8C;">
                            <th class="text-center">Kode <i class="fas fa-file-excel"></i></th>
                            <th class="text-center">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($cc_list as $cc): ?>
                            <tr>
                                <td class="text-center"><?= $cc->Comp_ID ?></td>
                                <td><?= $cc->Comp_Name ?>
                            </tr>

                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>