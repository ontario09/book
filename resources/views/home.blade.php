<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Book App</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
</head>

<body class="bg-secondary" style="--bs-bg-opacity: 0.5">
    <div class="container bg-light mt-4 border">
        <a href="javascript:void(0)" class="btn btn-success ml-3 mt-4" id="create-new-book">Add Book</a>
        <br /><br />
        <table class="table table-bordered table-striped" id="main-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>No</th>
                    <th>Cover</th>
                    <th>Name</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
        <div class="modal fade" id="ajax-book-modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="bookCrudModal"></h4>
                    </div>
                    <div class="modal-body">
                        <form id="bookForm" name="bookForm" class="form-horizontal" enctype="multipart/form-data">
                            <input type="hidden" name="book_id" id="book_id" />
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Name</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter Name" value="" maxlength="50" required="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Author</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="author" name="author"
                                        placeholder="Enter Author" value="" maxlength="50" required="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Description</label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" id="description" name="description" placeholder="Enter description" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Status</label>
                                <select class="form-select" aria-label="Default select example" id="status"
                                    name="status">
                                    <option value="1">Published</option>
                                    <option value="0">Not Published</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cover</label>
                                <div class="col-sm-12">
                                    <input id="image" type="file" name="image" accept="image/*"
                                        onchange="readURL(this);" />
                                    <input type="hidden" name="hidden_image" id="hidden_image" />
                                </div>
                            </div>
                            <img id="modal-preview" src="https://via.placeholder.com/150" alt="Preview"
                                class="form-group hidden" width="100" height="100" />
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary" id="btn-save" value="create">
                                    Save changes
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="detail-book-modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="bookDetailModalTitle">
                            Book Details
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <img id="detail-cover" src="https://via.placeholder.com/150" alt="Cover"
                                width="150" height="150" />
                        </div>
                        <div class="mb-3">
                            <h5 id="detail-name">Book Name</h5>
                        </div>
                        <div class="mb-3">
                            <p id="detail-description">
                                Description goes here...
                            </p>
                        </div>
                        <div class="mb-3">
                            <p>
                                <strong>Author:</strong>
                                <span id="detail-author">Author Name</span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <p>
                                <strong>Status:</strong>
                                <span id="detail-status" class="badge">Status</span>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var SITEURL = "http://127.0.0.1:8000/";
        console.log(SITEURL);
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            $("#main-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: SITEURL + "book",
                    type: "GET",
                },
                columns: [{
                        data: "id",
                        name: "id",
                        visible: false,
                    },
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "cover",
                        name: "cover",
                        orderable: false,
                    },
                    {
                        data: "name",
                        name: "name",
                    },
                    {
                        data: "author",
                        name: "author",
                    },
                    {
                        data: "status",
                        name: "status",
                    },
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                    },
                ],
                order: [
                    [0, "desc"]
                ],
            });
            $("#create-new-book").click(function() {
                $("#btn-save").val("create-book");
                $("#book_id").val("");
                $("#bookForm").trigger("reset");
                $("#bookCrudModal").html("Add New Book");
                $("#ajax-book-modal").modal("show");
                $("#modal-preview").attr(
                    "src",
                    "https://via.placeholder.com/150"
                );
            });
            $("body").on("submit", "#bookForm", function(e) {
                e.preventDefault();
                var actionType = $("#btn-save").val();
                $("#btn-save").html("Sending..");
                var formData = new FormData(this);
                console.log(formData);
                $.ajax({
                    type: "POST",
                    url: SITEURL + "book",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        console.log(data);
                        $("#bookForm").trigger("reset");
                        $("#ajax-book-modal").modal("hide");
                        $("#btn-save").html("Save Changes");
                        var oTable = $("#main-table").dataTable();
                        oTable.fnDraw(false);
                    },
                    error: function(data) {
                        console.log("Error:", data);
                        $("#btn-save").html("Save Changes");
                    },
                });
            });
            $("body").on("click", ".detail-book", function() {
                var book_id = $(this).data("id");
                $.get(SITEURL + "book/" + book_id, function(data) {
                    $("#bookDetailModalTitle").text(data.name);
                    $("#detail-name").text(data.name);
                    $("#detail-description").text(data.description);
                    $("#detail-author").text(data.author);

                    // Set cover image
                    if (data.cover) {
                        $("#detail-cover").attr(
                            "src",
                            SITEURL + "public/book/" + data.cover
                        );
                    } else {
                        $("#detail-cover").attr(
                            "src",
                            "https://via.placeholder.com/150"
                        );
                    }

                    // Set status and color
                    var statusText = data.status ?
                        "Published" :
                        "Not Published";
                    var statusClass = data.status ?
                        "bg-primary" :
                        "bg-danger";
                    $("#detail-status")
                        .text(statusText)
                        .removeClass("bg-primary bg-danger")
                        .addClass(statusClass);

                    // Show the modal
                    $("#detail-book-modal").modal("show");
                });
            });
            $("body").on("click", ".edit-book", function() {
                var book_id = $(this).data("id");
                console.log(book_id);
                $.get("book/" + book_id, function(data) {
                    $("#bookCrudModal").html("Edit Book");
                    $("#btn-save").val("edit-book");
                    $("#ajax-book-modal").modal("show");
                    $("#book_id").val(data.id);
                    $("#name").val(data.name);
                    $("#author").val(data.author);
                    $("#description").val(data.description);
                    $("#status").val(data.status);
                    $("#cover").val(data.cover);
                    $("#modal-preview").attr("alt", "No image available");
                    if (data.cover) {
                        $("#modal-preview").attr(
                            "src",
                            SITEURL + "public/book/" + data.cover
                        );
                        $("#hidden_image").attr(
                            "src",
                            SITEURL + "public/book/" + data.cover
                        );
                    }
                });
            });
            $("body").on("click", "#delete-book", function() {
                var book_id = $(this).data("id");
                if (confirm("Are You sure want to delete !")) {
                    $.ajax({
                        type: "DELETE",
                        url: SITEURL + "book/" + book_id,
                        success: function(data) {
                            var oTable = $("#main-table").dataTable();
                            oTable.fnDraw(false);
                        },
                        error: function(data) {
                            console.log("Error:", data);
                        },
                    });
                }
            });
        });

        function readURL(input, id) {
            id = id || "#modal-preview";
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(id).attr("src", e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
                $("#modal-preview").removeClass("hidden");
                $("#start").hide();
            }
        }
    </script>
</body>

</html>
