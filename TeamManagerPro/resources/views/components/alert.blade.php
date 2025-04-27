@if(session('success'))
    <div class="alert-success bg-[#00B140] text-white p-3 rounded mb-4 text-center  transition-opacity duration-300">
        {{ session('success') }}
    </div>
@endif


@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
