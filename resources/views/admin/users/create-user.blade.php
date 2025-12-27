@component('admin.layouts.content')
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                @include('admin.layouts.errors')
                <h4 class="card-title">Create User</h4>

                <form class="form-inline" method="POST" action="{{ route('store-user') }}">
                    @csrf
                    <label class="sr-only" for="name">Name</label>
                    <input type="text" class="form-control mb-2 mr-sm-2" id="name" placeholder="Enter User Name"
                        name="name">
                    <label class="sr-only" for="email">Email</label>
                    <input type="email" class="form-control mb-2 mr-sm-2" id="email" placeholder="Enter User Email"
                        name="email">
                    <label class="sr-only" for="phone">Phone</label>
                    <input type="text" class="form-control mb-2 mr-sm-2" id="phone" placeholder="Enter User Phone"
                        name="phone">
                    <label class="sr-only" for="address">Address</label>
                    <input type="text" class="form-control mb-2 mr-sm-2" id="address" placeholder="Enter User Address"
                        name="address">
                    <label class="sr-only" for="password">PassWord</label>
                    <input type="password" class="form-control mb-2 mr-sm-2" id="password"
                        placeholder="Enter User Password" name="password">
                    <label class="sr-only" for="password_confirmation">Password Confirmation</label>
                    <input type="password" class="form-control mb-2 mr-sm-2" id="password_confirmation"
                        placeholder="Retype the Password" name="password_confirmation">
                    <div>
                        <label class="sr-only" for="verify">User Verification</label>
                        <input type="checkbox" class="form-check-input mb-2 mr-sm-2" id="verify" name="verify">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endcomponent
