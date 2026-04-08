@extends('crm.layouts.vertical', ['page_title' => 'Roles', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    @vite(['node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css', 'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css', 'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css', 'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css', 'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css', 'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css'])
@endsection


@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    @can('role-add')
                        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">Add Role</a>
                    @endcan
                </div>
                <h4 class="m-0 pt-3">Roles</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                    <li class="breadcrumb-item active">Roles</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive-scroll">
                        <table id="role-table"
                            class="table table-bordered table-centered mb-0 dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th width="20%">Name</th>
                                    <th width="70%">Level</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@section('script')
    @vite(['resources/js/pages/demo.datatable-init.js'])
    <script>
        window.addEventListener('load', function() {

            $(function() {
                $('#role-table').DataTable({
                dom: 'rt<"bottom d-flex justify-content-between align-items-center"l p>',
                scrollY: "330px",
                scrollCollapse: true,
                paging: true,
                language: {
                    paginate: {
                        previous: "<i class='ri-arrow-left-s-line'>",
                        next: "<i class='ri-arrow-right-s-line'>"
                    }
                },
                drawCallback: function() {
                    $('#basic-datatable_paginate').addClass('pagination-rounded');
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('roles.index') }}",
                    data: function (d) {
                        d.role_search = $('#roleSearch').val();
                    }
                },

                columns: [
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'level',
                        name: 'level',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            const searchBoxHtml = `
                <div class="d-flex justify-content-end align-items-center mb-3">
                    <div style="max-width: 300px; width: 100%;">
                        <input type="text" 
                            id="roleSearch" 
                            class="form-control" 
                            placeholder="Search Role Name...">
                    </div>
                </div>
            `;

                // Insert search box same place where default search was
                $('#role-table_wrapper').prepend(searchBoxHtml);

                // Custom Search Function
                $('#roleSearch').on('keyup', function () {
                    table.ajax.reload();
                });
            });

            //Handle Delete
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This record will be permanently deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/roles/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.message, 'success');
                                $('#role-table').DataTable().ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });


            //Submit update
            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#edit_id').val();
                if (!$('#editForm')[0].checkValidity()) {
                    $('#editForm')[0].reportValidity();
                    return;
                }
                $.ajax({
                    url: `{{ url('roles') }}/${id}`,
                    type: 'POST',
                    data: $(this).serialize() + '&_method=PUT',
                    success: function(response) {

                        if (response.status) {
                            $('#editModal').modal('hide');
                            showToastmessage(response.message)
                            $('#role-table').DataTable().ajax.reload();
                        } else {
                            showToastmessage(response.message, 'error')
                        }

                    },
                    error: function(err) {
                        if (xhr.status === 422) {
                            // Clear old errors
                            $('.error-text').text('');

                            // Show validation messages
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                let input = $(`[name="${key}"]`);
                                input.addClass('is-invalid');
                                $(`#${key}_error`).text(value[0]);
                            });
                        }
                    }
                });
            });

        })
    </script>
@endsection
