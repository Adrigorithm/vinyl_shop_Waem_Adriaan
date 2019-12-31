@extends('layouts.template')

@section('title', 'Users')

@section('main')
    <h1>Users</h1>
    <form method="get" action="/admin/users" id="searchForm">
        <div class="row">
            <div class="col-sm-6 mb-2">
                <input type="text" class="form-control" name="nameemail" id="nameemail"
                       value="{{ request()->nameemail }}" placeholder="Filter Name Or Email">
            </div>
            <div class="col-sm-3 mb-2">
                <select class="form-control" name="userVar" id="userVar">
                    <option value="id"{{ (request()->userVar == "id" ? 'selected' : '') }}>Sort on:</option>
                    <option value="name"{{ (request()->userVar == "name" ? 'selected' : '') }}>Name</option>
                    <option value="email"{{ (request()->userVar == "email" ? 'selected' : '') }}>Email</option>
                    <option value="active"{{ (request()->userVar == "active" ? 'selected' : '') }}>Active</option>
                    <option value="admin"{{ (request()->userVar == "admin" ? 'selected' : '') }}>Admin</option>
                </select>
            </div>
            <div class="col-sm-3 mb-2">
                <select class="form-control" name="ascdesc" id="ascdesc">
                    <option value="asc"{{ (request()->ascdesc == "asc" ? 'selected' : '') }}>Ascending</option>
                    <option value="desc"{{ (request()->ascdesc == "desc" ? 'selected' : '') }}>Descending</option>
                </select>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th id="sessionId" data-id="{{auth()->id()}}">#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Active</th>
                <th>Admin</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
    <div id="pagination"></div>
    @include('admin.users.edit')
@endsection
@section('script_after')
    <script>
        var dataL;

        $(function () {
            loadTable();

            // submit form when leaving text field 'name-email'
            $('#nameemail').blur(function () {
                loadTable();
            });

            $('#userVar').change(function () {
                loadTable();
            });

            $('#ascdesc').change(function () {
                loadTable();
            });

            $('body').on('click', '#prevPage', function () {
                if (dataL.prev_page_url != null){
                    getData(dataL.prev_page_url);
                }
            });

            $('body').on('click', '#nextPage', function () {
                if (dataL.next_page_url != null){
                    getData(dataL.next_page_url);
                }
            });

            $('#modal-user form').submit(function (e) {
                // Don't submit the form
                e.preventDefault();
                // Get the action property (the URL to submit)
                let action = $(this).attr('action');
                // Serialize the form and send it as a parameter with the post
                let pars = $(this).serialize();
                // Post the data to the URL
                $.post(action, pars, 'json')
                    .done(function (data) {
                        console.log(data);
                        // Noty success message
                        new Noty({
                            type: data.type,
                            text: data.text
                        }).show();
                        // Hide the modal
                        $('#modal-user').modal('hide');
                        // Rebuild the table
                        getData('/admin/users/qryUsers/@/id/asc');
                    })
                    .fail(function (e) {
                        console.log('error', e);
                        // e.responseJSON.errors contains an array of all the validation errors
                        console.log('error message', e.responseJSON.errors);
                        // Loop over the e.responseJSON.errors array and create an ul list with all the error messages
                        let msg = '<ul>';
                        $.each(e.responseJSON.errors, function (key, value) {
                            msg += `<li>${value}</li>`;
                        });
                        msg += '</ul>';
                        // Noty the errors
                        new Noty({
                            type: 'error',
                            text: msg
                        }).show();
                    });
            });

            $('tbody').on('click', '.btn-delete', function () {
                // Get data attributes from td tag
                let id = $(this).closest('td').data('id');
                let name = $(this).closest('td').data('name');
                // Set some values for Noty
                let text = `<p>Delete the user <b>${name}</b>?</p>`;
                let type = 'warning';
                let btnText = 'Delete user';
                let btnClass = 'btn-success';

                // Show Noty
                let modal = new Noty({
                    timeout: false,
                    layout: 'center',
                    modal: true,
                    type: type,
                    text: text,
                    buttons: [
                        Noty.button(btnText, `btn ${btnClass}`, function () {
                            // Delete user and close modal
                            deleteUser(id);
                            modal.close();
                        }),
                        Noty.button('Cancel', 'btn btn-secondary ml-2', function () {
                            modal.close();
                        })
                    ]
                }).show();
            });

            $('tbody').on('click', '.btn-edit', function () {
                // Get data attributes from td tag
                let id = $(this).closest('td').data('id');
                let name = $(this).closest('td').data('name');
                let email = $(this).closest('td').data('email');
                let active = $(this).closest('td').data('active');
                let admin = $(this).closest('td').data('admin');
                // Update the modal
                $('.modal-title').text(`Edit ${name}`);
                $('form').attr('action', `/admin/users/${id}`);
                $('#name').val(name);
                $('#email').val(email);
                if (active === 1){
                    $('#active').prop("checked", true);
                }
                if (admin === 1){
                    $('#admin').prop("checked", true);
                }

                $('input[name="_method"]').val('put');
                // Show the modal
                $('#modal-user').modal('show');
            });
        });

        function deleteUser(id) {
            // Delete the genre from the database
            let pars = {
                '_token': '{{ csrf_token() }}',
                '_method': 'delete'
            };
            $.post(`/admin/users/${id}`, pars, 'json')
                .done(function (data) {
                    getData('/admin/users/qryUsers/@/id/asc');
                    new Noty({
                        type: data.type,
                        text: data.text
                    }).show();
                })
                .fail(function (e) {
                    console.log('error', e);
                });
        }

        function loadTable() {
            var nameemail = $('#nameemail').val();
            if (nameemail === ''){
                nameemail = '@';
            }
            var userVar = $('#userVar').val();
            var ascdesc = $('#ascdesc').val();
            $.getJSON('/admin/users/qryUsers/' + nameemail + '/' + userVar + '/' + ascdesc)
                .done(function (data) {
                    dataL = data;
                    console.log(dataL);
                    let tr;
                    // Clear tbody tag
                    $('tbody').empty();
                    // Loop over each item in the array
                    $.each(data.users.data, function (key, value) {
                        if (parseInt($('#sessionId').data('id')) === value.id){
                            tr = `<tr>
                               <td>${value.id}</td>
                               <td>${value.name}</td>
                               <td>${value.email}</td>
                               <td>${value.active}</td>
                               <td>${value.admin}</td>
                               <td data-id="${value.id}"
                                   data-name="${value.name}"
                                   data-email="${value.email}"
                                   data-active="${value.active}"
                                   data-admin="${value.admin}">
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-outline-success" style="cursor:not-allowed;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a class="btn btn-outline-danger" style="cursor:not-allowed;">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                               </td>
                           </tr>`;
                        }
                        else{
                            tr = `<tr>
                               <td>${value.id}</td>
                               <td>${value.name}</td>
                               <td>${value.email}</td>
                               <td>${value.active}</td>
                               <td>${value.admin}</td>
                               <td data-id="${value.id}"
                                   data-name="${value.name}"
                                   data-email="${value.email}"
                                   data-active="${value.active}"
                                   data-admin="${value.admin}">
                                    <div class="btn-group btn-group-sm">
                                        <a href="#!" class="btn btn-outline-success btn-edit" data-toggle="tooltip" title="Edit ${value.name}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#!" class="btn btn-outline-danger btn-delete" data-toggle="tooltip" title="Delete ${value.name}">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                               </td>
                           </tr>`;
                        }

                        // Append row to tbody
                        $('tbody').append(tr);
                    });
                    $('#pagination').empty();
                    if (dataL.users.next_page_url != null){
                        $('#pagination').html('<button id="prevPage"><< Previous</button><button id="nextPage">Next >></button>')
                    }
                    console.log(dataL.users.data.length);
                    if (dataL.users.data.length === 0){
                        $('#pagination').html('<div class="alert alert-danger alert-dismissible fade show">Can\'t find any user<button type="button" class="close" data-dismiss="alert"> <span>×</span> </button></div>')
                    }
                })
                .fail(function (e) {
                    console.log('error', e);
                })
        }

        function getData(url) {
            $.getJSON(url)
                .done(function (data) {
                    dataL = data;
                    let tr;
                    // Clear tbody tag
                    $('tbody').empty();
                    // Loop over each item in the array
                    $.each(data.users.data, function (key, value) {
                        if (parseInt($('#sessionId').data('id')) === value.id){
                            tr = `<tr>
                               <td>${value.id}</td>
                               <td>${value.name}</td>
                               <td>${value.email}</td>
                               <td>${value.active}</td>
                               <td>${value.admin}</td>
                               <td data-id="${value.id}"
                                   data-name="${value.name}"
                                   data-email="${value.email}"
                                   data-active="${value.active}"
                                   data-admin="${value.admin}">
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-outline-success" style="cursor:not-allowed;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a class="btn btn-outline-danger" style="cursor:not-allowed;">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                               </td>
                           </tr>`;
                        }
                        else{
                            tr = `<tr>
                               <td>${value.id}</td>
                               <td>${value.name}</td>
                               <td>${value.email}</td>
                               <td>${value.active}</td>
                               <td>${value.admin}</td>
                               <td data-id="${value.id}"
                                   data-name="${value.name}"
                                   data-email="${value.email}"
                                   data-active="${value.active}"
                                   data-admin="${value.admin}">
                                    <div class="btn-group btn-group-sm">
                                        <a href="#!" class="btn btn-outline-success btn-edit" data-toggle="tooltip" title="Edit ${value.name}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#!" class="btn btn-outline-danger btn-delete" data-toggle="tooltip" title="Delete ${value.name}">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                               </td>
                           </tr>`;
                        }
                        // Append row to tbody
                        $('tbody').append(tr);
                    });
                    $('#pagination').empty();
                    if (dataL.users.next_page_url != null){
                        $('#pagination').html('<button id="prevPage"><< Previous</button><button id="nextPage">Next >></button>')
                    }
                    if (dataL.users.data.length === 0){
                        $('#pagination').html('<div class="alert alert-danger alert-dismissible fade show">Can\'t find any user<button type="button" class="close" data-dismiss="alert"> <span>×</span> </button></div>')
                    }
                })
                .fail(function (e) {
                    console.log('error', e);
                })
        }
    </script>
@endsection
