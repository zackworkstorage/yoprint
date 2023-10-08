@extends ('layout')

@section('content')

<div class="p-3">
    <a href="{{ route('product.index') }}" class="btn btn-primary">Go Back</a>
    <hr/>
    <table id="myTable">
        <thead>
            <tr>
                <th>Unique Key</th>
                <th>Product Title</th>
                <th>Description</th>
                <th>Style</th>
                <th>Sanmar Mainframe Color</th>
                <th>Size</th>
                <th>Color Name</th>
                <th>Piece Price</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($results))
            @foreach($results as $result)
            <tr>
                <td>{{ $result->unique_key }}</td>
                <td>{{ $result->product_title }}</td>
                <td>{{ $result->product_description }}</td>
                <td>{{ $result->style }}</td>
                <td>{{ $result->sanmar_mainframe_color }}</td>
                <td>{{ $result->size }}</td>
                <td>{{ $result->color_name }}</td>
                <td>{{ $result->piece_price }}</td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
jQuery(document).ready(function(){
    let table = new DataTable('#myTable');
});

</script>
@endsection


