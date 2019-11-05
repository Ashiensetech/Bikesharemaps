/* Profile Photo Upload */
$(document).ready(function(){
    var profiledropZone = new Dropzone('#video_file', {
        //url: "http://server.jobjacker.vm:8039/index.php?control=Documents&action=fileupload",
        url: "http://server.jobjacker.vm:8039/api/file_profile_image/create",
        init: function () {
            this.on("success", function (file, responseText) {

                document.getElementById('profile_file_name').value = responseText['file_name'];
                console.log("Hello World!!!!");
                console.log(file);
                console.log(responseText);
                console.log("Hello World!!!!2222");

            });
        },
        maxFiles: 1,
        maxfilesexceeded: function (file) {
            this.removeAllFiles();
            this.addFile(file);
        },
        acceptedFiles: ".jpg,.png,.gif",
        // addRemoveLinks:true
    });
});

if (document.getElementById('resume-drop')) {
    var resumedropZone = new Dropzone('#resume-drop', {
        url: "http://server.jobjacker.vm:8039/api/file_cv/create",
        init: function () {
            this.on("success", function (file, responseText) {
                document.getElementById('resume_file_name').value = responseText['file_name'];
                // console.log(file);
                console.log(responseText);
            });
        },
        maxFiles: 1,
        maxfilesexceeded: function (file) {
            this.removeAllFiles();
            this.addFile(file);
        },
        acceptedFiles: ".mp4,.wmv,.avi,.doc,.docx,.pdf",
        // addRemoveLinks:true,
        // removedfile: function(file) {
        //     var _ref;
        //     console.log(file.name);
        //     deleteFile(file.name);
        //     return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        // }
    });
}


function deleteFile(filename) {
    url = "/index.php?control=Documents&action=filedelete";
    $.ajax({
        url: url,
        async: false,
        data: {'filetodelete': filename},
        success: function (data) {
            console.log(data);
        }
    });
}
