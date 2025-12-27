@component('admin.layouts.content')
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                @include('admin.layouts.errors')
                <h4 class="card-title">Create Category</h4>

                <form class="form-inline" method="POST" action="{{ route('store-category') }}">
                    @csrf
                    <label class="sr-only" for="inlineFormInputName2">Category Name</label>
                    <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2"
                        placeholder="Enter Category Name" name="name">

                    <button type="submit" class="btn btn-primary mb-2">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endcomponent
