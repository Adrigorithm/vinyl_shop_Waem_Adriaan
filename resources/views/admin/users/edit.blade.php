<div class="modal" id="modal-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">modal-genre-title</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    @method('')
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name"
                               class="form-control"
                               placeholder="Name"
                               minlength="3"
                               required
                               value="">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email"
                               class="form-control"
                               placeholder="=Email"
                               minlength="3"
                               required
                               value="">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="active">Active</label>
                        <input type="checkbox" name="active" id="active"
                               class="form-control"
                               style="width:30px;height:30px"
                               required
                               value="active">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="admin">Admin</label>
                        <input type="checkbox" name="admin" id="admin"
                               class="form-control"
                               style="width:30px;height:30px"
                               required
                               value="admin">
                        <div class="invalid-feedback"></div>
                    </div>
                    <button type="submit" class="btn btn-success">Save user</button>
                </form>
            </div>
        </div>
    </div>
</div>
