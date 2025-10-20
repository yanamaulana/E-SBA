<div class="modal fade" id="ModalAttachment" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="ModalAttachmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="ModalAttachmentLabel">List Attachment <?= $CbrNo; ?></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-3 table-responsive">
                    <table class="table-sm table-striped table-hover table-bordered w-100">
                        <thead>
                            <tr style="background-color: #CFE2FF;">
                                <th class="text-center">#</th>
                                <th class="text-center">File Name</th>
                                <th class="text-center">Doc Type</th>
                                <th class="text-center">Note</th>
                                <!-- <th class="text-center"><i class="fas fa-cogs text-dark"></i></th> -->
                            </tr>
                        </thead>
                        <tbody id="tbody-attachment">
                            <?php if ($Attachments->num_rows() > 0) : ?>
                                <?php $i = 1; ?>
                                <?php foreach ($Attachments->result() as $li) : ?>
                                    <tr>
                                        <td class="text-center"><?= $i; ?></td>
                                        <td class="">
                                            <a target='_blank' href="<?= base_url() ?>assets/Files/AttachmentCbr/<?= $li->Year_Upload ?>/<?= $li->AttachmentType ?> /<?= $li->Attachment_FileName; ?>"><?= $li->Attachment_FileName; ?></a>
                                        </td>
                                        <td class=""><?= $li->AttachmentType; ?></td>
                                        <td class=""><?= $li->Note; ?></td>
                                    </tr>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">This Cbr Doesnt Have Attachment</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="fas fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>