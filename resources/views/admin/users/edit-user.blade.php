@component('admin.layouts.content')
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                @include('admin.layouts.errors')
                <h4 class="card-title">Edit User</h4>

                <form class="form-inline" method="POST" action="{{ route('update-user', [$user->id]) }}">
                    @csrf
                    @method('put')
                    <label class="sr-only" for="name">Name</label>
                    <input type="text" class="form-control mb-2 mr-sm-2" id="name" placeholder="Enter User Name"
                        name="name" value="{{ old('name', $user->name) }}">
                    <label class="sr-only" for="email">Email</label>
                    <input type="email" class="form-control mb-2 mr-sm-2" id="email" placeholder="Enter User Email"
                        name="email" value="{{ old('email', $user->email) }}">
                    <label class="sr-only" for="phone">Phone</label>
                    <input type="text" class="form-control mb-2 mr-sm-2" id="phone" placeholder="Enter User Phone"
                        name="phone" value="{{ old('phone', $user->phone) }}">
                    <label class="sr-only" for="address">Address</label>
                    <input type="text" class="form-control mb-2 mr-sm-2" id="address" placeholder="Enter User Address"
                        name="address" value="{{ old('address', $user->address) }}">
                    <label class="sr-only" for="password">PassWord</label>
                    <input type="password" class="form-control mb-2 mr-sm-2" id="password"
                        placeholder="Enter User Password" name="password">
                    <label class="sr-only" for="password_confirmation">Password Confirmation</label>
                    <input type="password" class="form-control mb-2 mr-sm-2" id="password_confirmation"
                        placeholder="Retype the Password" name="password_confirmation">
                    @if (!$user->hasVerifiedEmail())
                        <div>
                            <label class="sr-only" for="verify">User Verification</label>
                            <input type="checkbox" class="form-check-input mb-2 mr-sm-2" id="verify" name="verify">
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary mb-2">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endcomponent
