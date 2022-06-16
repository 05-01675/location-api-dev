@extends('banner.Layout')
@section('content')
    <div class="d-flex flex-row-reverse bd-highlight">
        <a href="/banners/create" class="btn btn-success mb-2">Add</a> 
    </div>
    
    <br>
    <div class="row">
        <div class="col-12">
        <table class="table table-striped table-hover" id="laravel_crud">
            <thead>
                <tr class="text-center">
                    <!-- <th scope="row">Id</th> -->
                    <th>Image</th>
                    <th>Redirect Type</th>
                    <th>Link</th>
                    <th>Status</th>
                    <th>Created at</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($banners as $banner)
                <tr class="text-center">
                    <!-- <th scope="row">{{ $banner->id }}</th> -->
                    <td>{{ $banner->image }}</td>
                    <td>{{ $banner->redirect_type }}</td>
                    <td>{{ $banner->image_url }}</td>
                    <td>{{ $banner->status == 1 ? "Active" : "Inactive" }}</td>
                    <td>{{ date('Y-m-d', strtotime($banner->created_at)) }}</td>
                    <td class="d-flex"><a href="banners/edit/{{$banner->id}}" class="btn btn-primary mx-3">Edit</a>
                        <form action="/banners/destroy/{{$banner->id}}" method="post">
                            {{ csrf_field() }}
                            @method('DELETE')
                            <button class="btn btn-danger" onclick="return confirm('Delete banner?')" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {!! $banners->links() !!}
        </div> 
    </div>
@endsection  