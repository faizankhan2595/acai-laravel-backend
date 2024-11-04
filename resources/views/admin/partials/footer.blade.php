<footer class="footer text-right">
    {{config('admin.copyright')}}
</footer>
<div id="adminpassword-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content p-0 b-0">
            <div class="panel panel-color panel-primary">
                <div class="panel-heading">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="panel-title">Chnage Password</h3>
                </div>
                <div class="panel-body">
                    <form action="{{ route('admin.password') }}" method="post" data-parsley-validate novalidate id="admin-password">
                        @csrf
                        <div class="custom-modal-text text-left">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="Password" name="password" placeholder="Password" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Password Confirmation" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal" aria-hidden="true">Close</button>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@push('scripts')
    <script>
        $("#admin-password").on('submit',function(e) {
            e.preventDefault();
            if ( $(this).parsley().isValid() ) {
                $.ajax({
                    url : '{{ route('admin.password') }}',
                    type: 'POST',
                    data: $("#admin-password").serialize(),
                    beforeSend : function() {
                        $("#admin-password").find('button').attr('disabled',true);
                    },
                    complete : function() {
                        $("#admin-password").find('button').attr('disabled',false);
                    },
                    success : function(data) {
                        toastr["success"](data.message);
                        $("#admin-password")[0].reset();
                        $('#adminpassword-modal').modal('hide');
                    },
                    error : function(data) {
                        toastr["error"](data.responseJSON.errors[Object.keys(data.responseJSON.errors)]);
                    }
                })
            }
        })
    </script>
@endpush
