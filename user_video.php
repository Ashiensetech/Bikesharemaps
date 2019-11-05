<?php require('user_header.php') ?>
    <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="css/responsive.bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css"/>
    <div role="tabpanel">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#video-tutorials" aria-controls="videos" role="tab"
                                                      data-toggle="tab"><span
                            class="glyphicon glyphicon-film" aria-hidden="true"></span> <?php echo _('Videos'); ?>
                </a></li>
            <li role="presentation"><a href="#lock-tutorials" aria-controls="locks" role="tab" data-toggle="tab"><span
                            class="glyphicon glyphicon-lock" aria-hidden="true"></span> <?php echo _('Lock'); ?>
                </a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="video-tutorials">
                <div id="row">
                    <table id="video-list" class="display" style="width:100%">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Video</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="lock-tutorials">
                <div id="row">
                    <div class="col-md-12 col-xs-12">
                        <img src="./images/lock.jpg" alt="" style="max-width: 100%; margin: auto; border: 6px solid #053643; box-shadow: 0px 0px 0px 10px #015166; display: block; margin-top: 20px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/dataTables.responsive.min.js"></script>
    <script>
        $(function () {
            $(document).ready(function () {
                $('#video-list').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": "route?action=video-list",
                    "columnDefs": [{
                        "targets": 2,
                        "data": null,
                        "render": function (data, type, row, meta) {
                            var html = '<div class="image-holder"><a type="button" data-toggle="modal" data-target="#myModal" class="video-thumb">' +
                                '<i class="fa fa-play-circle"></i><img style="width:200px;" src="'+ data[3] +'" ></a></div>' +
                                '<div class="modal fade video-modal" id="myModal" role="dialog">' +
                                '<div class="modal-dialog">' +
                                '<div class="modal-content">' +
                                '<div class="modal-body">' +
                                '<video name="videoview"  width="100%" height="400" controls> +' +
                                '<source src="  '+ data[2] +' " type="video/mp4"> +' +
                                '</video>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                            return html;
                        }
                    }]

                })
            });
        })
    </script>

<script>
    $('body').on('hide.bs.modal', '.video-modal', function () {
        var video =  $(this).find('video')[0];
        video.currentTime = 0;
        video.pause();
    })
</script>
<?php require('user_footer.php') ?>

