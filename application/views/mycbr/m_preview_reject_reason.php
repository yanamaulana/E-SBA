<div class="modal fade" id="ModalRejectReason" tabindex="-1" aria-labelledby="ModalRejectReasonLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="ModalRejectReasonLabel">CBR Submission Reject Reason</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="rounded border p-10">
                        <?php foreach ($CBReq_No_Resubmissions as $CBReq_No): ?>
                            <div class="mb-10">
                                <label class="form-label">Reject Reason CBR : <?= $CBReq_No ?></label>
                                <?php $RowRejectCbr = $this->db->get_where('Thst_Trx_Cbr_Approval', ['CBReq_No' => $CBReq_No])->result(); ?>
                                <?php foreach ($RowRejectCbr as $li): ?>
                                    <textarea class="form-control mt-2"><?= $li->Rejection_Reason ?></textarea>
                                <?php endforeach; ?>
                            </div>
                            <hr>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="fas fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>