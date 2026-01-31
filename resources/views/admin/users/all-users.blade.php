@component('admin.layouts.content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title">Users</h4>
                    <a class="nav-link btn btn-success create-new-button" href="{{ route('create_user') }}">+ Create New
                        User</a>
                </div>
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> Name </th>
                                <th> Email </th>
                                <th> Phone </th>
                                <th> Address </th>
                                <th> Email Status </th>
                                <th>Role</th>   {{-- ستون جدید --}}
                                <th>Action </th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($users as $user)
                                <tr>
                                    <td> {{ $user->id }} </td>
                                    <td> {{ $user->name }} </td>
                                    <td> {{ $user->email }} </td>
                                    <td> {{ $user->phone }} </td>
                                    <td> {{ $user->address }} </td>
                                    <td>
                                        @if ($user->email_verified_at)
                                            <span class="badge badge-success">ACtive</span>
                                        @else
                                            <span class="badge badge-danger">InACtive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @role('admin')
                                            @include('admin.users.change-role', ['user' => $user])
                                            @if($user->roles->isNotEmpty())
                                                <span class="badge bg-info">
                                                    {{ $user->roles->first()->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">بدون نقش</span>
                                            @endif
                                            {{-- تغییر نقش --}}
                                        @endrole
                                        @unlessrole('admin')
                                            <span class="text-muted">دسترسی ندارد</span>
                                        @endunlessrole

                                        
                                    </td>
                                    <td>
                                        @role('admin')
                                            <a href="{{ route('edit-user', $user->id) }}" class="btn btn-sm btn-info">Edit</a>
                                            <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                        @endrole
                                        @unlessrole('admin')
                                            <span class="text-muted">دسترسی ندارد</span>
                                        @endunlessrole

                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endcomponent
