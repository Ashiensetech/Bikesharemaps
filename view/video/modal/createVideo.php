<div class="modal fade add-video-modal bd-example-modal-lg" tabindex="-1" role="dialog"
     aria-labelledby="add_video_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="add_video_Label">
                    <?php echo _('Create Video') ?>
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
                <form class="container" id="addvideo" enctype="multipart/form-data">
                    <div><h3><?php echo _('New Video') ?></h3></div>
                    <div class="form-group"><label for="filename"><?php echo _('Filename:'); ?></label> <input
                                type="text" name="filename" id="video_filename" class="form-control"/></div>
                    <div class="form-group"><label for="file"><?php echo _('Video: (200 MB Max)'); ?></label> <input
                                type="file" class="dropzone" name="file" id="video_file"/></div>
                    <div class="form-group"><label for="thumbnail"><?php echo _('Thumbnail:'); ?></label> <input
                                type="file" class="dropzone" name="thumbnail" id="video_thumbnail"/></div>
                    <button type="button" id="savenewvideo"
                            class="btn btn-primary"><?php echo _('Save'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

