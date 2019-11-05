Dropzone.options.documentsDrop = {
    init: function () {
        // Set up any event handlers
        this.on('complete', function () {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                reloadFiles();
            }
        });
    }
};

function reloadFiles() {
    $.ajax({
        url: location.url,
        async: false,
        success: function(data) {
            window.newfilesht = data ; }
    });
    ht = $($.parseHTML(window.newfilesht)).find('#updatable') ;
    $('#updatable').html(ht);
}
