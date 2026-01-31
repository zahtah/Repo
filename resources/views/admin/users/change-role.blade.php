<form action="{{ route('users.changeRole', $user->id) }}"
      method="POST"
      style="display:inline">

    @csrf
    @method('PUT')

    <select name="role"
            onchange="this.form.submit()"
            class="form-select form-select-sm d-inline w-auto">

        @foreach($roles as $role)
            <option value="{{ $role->name }}"
                {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach

    </select>
</form>
