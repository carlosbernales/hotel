<?php
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}
?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                    <em class="fa fa-home"></em>
                </a></li>
            <li class="active">Room Types</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Room Types
                    <button class="btn btn-secondary pull-right" style="border-radius:0%" data-toggle="modal" data-target="#addRoomType">Add Room Type</button>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Room Type</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $room_query = "SELECT * FROM room_type WHERE deleteStatus = 0";
                            $result = mysqli_query($connection, $room_query);
                            if (mysqli_num_rows($result) > 0) {
                                while ($room_types = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $room_types['room_type'] ?></td>
                                        <td><?php echo $room_types['price'] ?></td>
                                        <td><?php echo $room_types['description'] ?></td>
                                        <td>
                                            <button title="Edit Room Type" style="border-radius:60px;" data-toggle="modal"
                                                    data-target="#editRoomType" data-id="<?php echo $room_types['room_type_id']; ?>"
                                                    class="btn btn-primary"><i class="fa fa-edit"></i></button>
                                            <a href="ajax.php?delete_room_type=<?php echo $room_types['room_type_id']; ?>"
                                               class="btn btn-danger" style="border-radius:60px;" onclick="return confirm('Are you sure you want to delete this room type?');">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Room Type Modal -->
<div id="addRoomType" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Room Type</h4>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <div class="form-group">
                        <label>Room Type</label>
                        <input type="text" class="form-control" name="room_type" placeholder="Room Type" required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" name="price" placeholder="Price" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" placeholder="Description" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" name="add_room_type">Add Room Type</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Room Type Modal -->
<div id="editRoomType" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Room Type</h4>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <input type="hidden" id="edit_room_type_id" name="room_type_id">
                    <div class="form-group">
                        <label>Room Type</label>
                        <input type="text" class="form-control" id="edit_room_type_name" name="room_type" required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" id="edit_price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="edit_description" name="description" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" name="edit_room_type">Update Room Type</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#editRoomType').on('show.bs.modal', function(e) {
        var roomTypeId = $(e.relatedTarget).data('id');
        $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: {get_room_type: true, room_type_id: roomTypeId},
            dataType: 'json',
            success: function(response) {
                $('#edit_room_type_id').val(response.room_type_id);
                $('#edit_room_type_name').val(response.room_type);
                $('#edit_price').val(response.price);
                $('#edit_description').val(response.description);
            }
        });
    });
});
</script>
