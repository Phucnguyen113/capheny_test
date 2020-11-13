@extends('layouts.admin')
@section('body')
    <div class="card">
        <div class="card-body">
            <form action="" method="post">
                @csrf 
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <label for="color">Kích cỡ <span style="color:Red"> *</span></label>
                        <select name="size[]" id="size" multiple class="form-control">
                            @foreach($list_size as $sizes =>  $size)
                                <option value="{{$size->size_id}}">{{$size->size}}</option>
                            @endforeach
                        </select>
                        <div id="color_error"  style="color:Red" class="p_error">
                                @if($errors->has('size'))
                                    {{$errors->first('size')}}
                                @endif
                            </div>
                    </div>
                </div>
                <script>
                
                    $("#size").select2({
                        placeholder: "Chọn kích cỡ",
                    });
                </script>
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success">Thêm kích cỡ</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection