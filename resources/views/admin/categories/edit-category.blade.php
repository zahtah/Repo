@component('admin.layouts.content')
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Category</h4>

                <form class="form-inline" method="POST" action="{{ route('update-category', $category->id) }}">
                    @csrf
                    @method('PUT');
                    <label class="sr-only" for="inlineFormInputName2">Category Name</label>
                    <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2"
                        placeholder="Enter Category Name" name="name" value="{{ old('name', $category->name) }}">

                    <button type="submit" class="btn btn-primary mb-2">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endcomponent
