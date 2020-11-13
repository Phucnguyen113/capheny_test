@extends('layouts.admin')
@section('body')
   
    <div class="card">
        <div class="card-body">
            <form action="" method="post">
                @csrf 
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <label for="color">Màu <span style="color:Red"> *</span></label>
                        <select name="color[]" id="color" multiple class="form-control">
                            @foreach($list_color as $colors =>  $color)
                                <option value="{{$color->color_id}}" data-color="{{$color->color}}"></option>
                            @endforeach
                        </select>
                        <div id="color_error"  style="color:Red" class="p_error">
                                @if($errors->has('color'))
                                    {{$errors->first('color')}}
                                @endif
                            </div>
                    </div>
                </div>
                <script>
                    function formatState (state) {
                        if (!state.id) {
                            return state.text;
                        }
                        var color=$(state.element).attr('data-color');
                        var $state = $(
                        '<span><div style="width:15px;height:15px;background-color:#'+color+';display:inline-block"></div></span>'
                        );
                        return $state;
                    };

                    $("#color").select2({
                        templateResult: formatState,
                        templateSelection :formatState,
                        placeholder: "Chọn màu",

                    });
                </script>
                <div class="form-row form-group">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success">Thêm màu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection