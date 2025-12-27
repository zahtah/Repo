@component('admin.layouts.content')
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title">Categories</h4>
                    <a class="nav-link btn btn-success create-new-button" href="{{ route('createCategories') }}">+ Create New
                        Category</a>
                </div>
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> Category name </th>
                                <th>Action </th>
                            </tr>
                        </thead>
                        <tbody>

                            <form action="{{ route('allocations.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="file" required>
                                <button type="submit">آپلود اکسل</button>
                            </form>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endcomponent
